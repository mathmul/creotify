<?php

declare(strict_types=1);

namespace App\Controller\Web;

use App\Repository\Contract\ArticleRepositoryInterface;
use App\Repository\Contract\SubscriptionPackageRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/', name: 'web_demo_')]
final class DemoController extends AbstractController
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly SubscriptionPackageRepositoryInterface $subscriptionRepository,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('demo/index.html.twig', [
            'articles' => $this->articleRepository->findAll(),
            'subscriptions' => $this->subscriptionRepository->findAll(),
        ]);
    }

    #[Route('/orders', name: 'orders', methods: ['GET'])]
    public function orders(Request $request): Response
    {
        $phone = $request->query->get('phoneNumber');
        $orders = [];

        if ($phone) {
            $response = $this->httpClient->request('GET', sprintf('%s/api/orders?phoneNumber=%s',
                $this->getParameter('app.base_url'),
                urlencode($phone)
            ));
            $orders = $response->toArray();
        }

        return $this->render('demo/orders.html.twig', [
            'orders' => $orders,
            'phoneNumber' => $phone,
        ]);
    }

    #[Route('/order', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request): Response
    {
        $phone = $request->request->get('phoneNumber');
        $selection = (string) $request->request->get('selection');
        $parts = explode(':', $selection, 2);
        $itemType = $parts[0];
        $itemId = isset($parts[1]) ? (int) $parts[1] : 0;

        $response = $this->httpClient->request('POST', sprintf('%s/api/orders', $this->getParameter('app.base_url')), [
            'json' => [
                'phoneNumber' => $phone,
                'items' => [['type' => $itemType, 'id' => $itemId]],
            ],
        ]);

        $result = $response->toArray(false);

        return $this->render('demo/result.html.twig', [
            'response' => $result,
            'status' => $response->getStatusCode(),
        ]);
    }
}
