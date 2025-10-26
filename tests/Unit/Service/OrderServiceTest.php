<?php

declare(strict_types=1);

use App\Entity\Article;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\SubscriptionPackage;
use App\Repository\ArticleRepository;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionPackageRepository;
use App\Service\Exception\DuplicatePurchaseException;
use App\Service\Exception\SubscriptionAlreadyExistsException;
use App\Service\OrderService;
use App\Tests\Traits\EntityHelper;

uses(EntityHelper::class);

beforeEach(function () {
    $this->orderRepo = Mockery::mock(OrderRepository::class);
    $this->customerRepo = Mockery::mock(CustomerRepository::class);
    $this->articleRepo = Mockery::mock(ArticleRepository::class);
    $this->subscriptionRepo = Mockery::mock(SubscriptionPackageRepository::class);

    $this->service = new OrderService(
        $this->orderRepo,
        $this->customerRepo,
        $this->articleRepo,
        $this->subscriptionRepo,
    );
});

it('creates a valid order successfully', function () {
    $customerPhone = '+38612345678';
    $customer = new Customer($customerPhone);
    $this->setEntityId($customer, 1);

    $articleId = 1;
    $article = (new Article())
        ->setName('Test')
        ->setPrice('19.99')
        ->setSupplierEmail('a@b.c');
    $this->setEntityId($article, $articleId);

    $this->customerRepo->shouldReceive('findOneBy')->andReturn($customer);
    $this->articleRepo->shouldReceive('find')->andReturn($article);
    $this->orderRepo->shouldReceive('customerHasArticle')->andReturnFalse();
    $this->orderRepo->shouldReceive('save')->once();

    $order = $this->service->createOrder($customerPhone, [
        ['type' => 'article', 'id' => $articleId],
    ]);

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->getCustomer())->toBe($customer)
        ->and($order->getTotalPrice())->toBe('19.99');
});

it('throws on duplicate article', function () {
    $customerId = 1;
    $customerPhone = '+38612345678';
    $customer = new Customer($customerPhone);
    $this->setEntityId($customer, $customerId);

    $articleId = 1;
    $article = (new Article())
        ->setName('Test')
        ->setPrice('19.99')
        ->setSupplierEmail('a@b.c');
    $this->setEntityId($article, $articleId);

    $this->customerRepo->shouldReceive('findOneBy')->andReturn($customer);
    $this->articleRepo->shouldReceive('find')->andReturn($article);
    $this->orderRepo->shouldReceive('customerHasArticle')->andReturnTrue();

    $this->service->createOrder($customerPhone, [
        ['type' => 'article', 'id' => $articleId],
    ]);
})->throws(DuplicatePurchaseException::class);

it('throws on duplicate subscription', function () {
    $customerPhone = '+38612345678';
    $customer = new Customer($customerPhone);
    $this->setEntityId($customer, 1);

    $subscriptionPackageId = 1;
    $subscriptionPackage = (new SubscriptionPackage())
        ->setName('Test')
        ->setPrice('19.99');
    $this->setEntityId($subscriptionPackage, $subscriptionPackageId);

    $this->customerRepo->shouldReceive('findOneBy')->andReturn($customer);
    $this->subscriptionRepo->shouldReceive('find')->andReturn($subscriptionPackage);
    $this->orderRepo->shouldReceive('customerHasSubscription')->andReturnTrue();

    $this->service->createOrder($customerPhone, [
        ['type' => 'subscription', 'id' => 1],
    ]);
})->throws(SubscriptionAlreadyExistsException::class);
