<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\Contract\ArticleRepositoryInterface;
use App\Repository\Contract\CustomerRepositoryInterface;
use App\Repository\Contract\OrderItemRepositoryInterface;
use App\Repository\Contract\OrderRepositoryInterface;
use App\Repository\Contract\SubscriptionPackageRepositoryInterface;
use App\Service\Exception\DuplicatePurchaseException;
use App\Service\Exception\SubscriptionAlreadyExistsException;

class OrderService
{
    public function __construct(
        private readonly ArticleRepositoryInterface $articleRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SubscriptionPackageRepositoryInterface $subscriptionPackageRepository,
    ) {
    }

    public function createOrder(string $customerPhone, array $items): Order
    {
        /** @var Customer $customer */
        $customer = $this->customerRepository->findOneBy(['phoneNumber' => $customerPhone]);
        $customer ??= new Customer($customerPhone);
        $this->customerRepository->save($customer);

        $order = new Order($customer);

        foreach ($items as $item) {
            if ('article' === $item['type']) {
                if ($this->orderRepository->customerHasArticle($customer, $item['id'])) {
                    ray('article duplicate');
                    throw new DuplicatePurchaseException();
                }
                /** @var Article $article */
                $article = $this->articleRepository->find($item['id']);
                $orderItem = new OrderItem($order, 'article', $article->getId(), $article->getPrice());
                $order->addOrderItem($orderItem);
            } elseif ('subscription' === $item['type']) {
                if ($this->orderRepository->customerHasSubscription($customer)) {
                    ray('subscription duplicate');
                    throw new SubscriptionAlreadyExistsException();
                }
                /** @var SubscriptionPackage $subscription */
                $subscription = $this->subscriptionPackageRepository->find($item['id']);
                $orderItem = new OrderItem($order, 'subscription', $subscription->getId(), $subscription->getPrice());
                $order->addOrderItem($orderItem);
            }
        }

        $this->orderRepository->save($order);

        return $order;
    }
}
