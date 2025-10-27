<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\Contract\OrderRepositoryInterface;
use App\Service\Exception\DuplicatePurchaseException;
use App\Service\Exception\SubscriptionAlreadyExistsException;
use App\Service\OrderService;
use App\Service\SMS\Contract\SmsManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/orders', name: 'api_orders_')]
final class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SmsManagerInterface $smsManager,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        if (!isset($data['phoneNumber'], $data['items']) || !is_array($data['items'])) {
            return $this->json(['error' => 'Invalid payload'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $order = $this->orderService->createOrder($data['phoneNumber'], $data['items']);

            // Send SMS confirmation (rate-limited)
            $this->smsManager->sendSMS(
                $data['phoneNumber'],
                sprintf('Order %s created successfully.', $order->getOrderNumber())
            );

            return $this->json([
                'orderNumber' => $order->getOrderNumber(),
                'customerPhone' => $data['phoneNumber'],
                'status' => $order->getStatus(),
                'totalPrice' => $order->getTotalPrice(),
                'items' => array_map(fn ($i) => [
                    'type' => $i->getItemType(),
                    'id' => $i->getItemId(),
                    'price' => $i->getPrice(),
                ], $order->getOrderItems()->toArray()),
            ], Response::HTTP_CREATED);
        } catch (DuplicatePurchaseException|SubscriptionAlreadyExistsException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'list', methods: ['GET'], options: ['requires_query' => true])]
    public function list(Request $request): JsonResponse
    {
        $phone = $request->query->get('phoneNumber');
        if (!$phone) {
            return $this->json(['error' => 'Missing phoneNumber parameter'], Response::HTTP_BAD_REQUEST);
        }

        $orders = $this->orderRepository->createQueryBuilder('o')
            ->select('o')
            ->leftJoin('o.customer', 'c')
            ->where('c.phoneNumber = :phone')
            ->setParameter('phone', $phone)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $data = array_map(fn ($o) => [
            'orderNumber' => $o->getOrderNumber(),
            'status' => $o->getStatus(),
            'totalPrice' => $o->getTotalPrice(),
            'createdAt' => $o->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $orders);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/{orderNumber}', name: 'show', methods: ['GET'])]
    public function show(string $orderNumber): JsonResponse
    {
        $order = $this->orderRepository->findOneBy(['orderNumber' => $orderNumber]);

        if (!$order) {
            return $this->json(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'orderNumber' => $order->getOrderNumber(),
            'status' => $order->getStatus(),
            'totalPrice' => $order->getTotalPrice(),
            'items' => array_map(fn ($i) => [
                'type' => $i->getItemType(),
                'id' => $i->getItemId(),
                'price' => $i->getPrice(),
            ], $order->getOrderItems()->toArray()),
        ], Response::HTTP_OK);
    }

    #[Route('/{orderNumber}', name: 'cancel', methods: ['DELETE'])]
    public function cancel(string $orderNumber): JsonResponse
    {
        $order = $this->orderRepository->findOneBy(['orderNumber' => $orderNumber]);
        if (!$order) {
            return $this->json(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        // Soft delete or status update — logic stays in service layer if extended later
        $this->orderRepository->remove($order);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
