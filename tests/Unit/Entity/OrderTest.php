<?php

declare(strict_types=1);

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;

it('generates an order number automatically', function () {
    $customer = new Customer('555');
    $order = new Order($customer);

    expect($order->getOrderNumber())->toStartWith('ORD-');

    $orderNumberLength = strlen($order->getOrderNumber());
    expect($orderNumberLength)->toBeGreaterThan(4);
});

it('adds and removes order items', function () {
    $customer = new Customer('888');
    $order = new Order($customer);
    $item = new OrderItem($order, 'article', 1, '19.99');

    $order->addOrderItem($item);
    expect($order->getOrderItems())->toHaveCount(1);

    $order->removeOrderItem($item);
    expect($order->getOrderItems())->toHaveCount(0);
});

it('updates total price when items are added or removed', function () {
    $customer = new Customer('999');
    $order = new Order($customer);

    $item1 = new OrderItem($order, 'article', 1, '10.00');
    $item2 = new OrderItem($order, 'article', 2, '5.50');

    expect($order->getTotalPrice())->toBe('0.00');

    $order->addOrderItem($item1);
    expect($order->getTotalPrice())->toBe('10.00');

    $order->addOrderItem($item2);
    expect($order->getTotalPrice())->toBe('15.50');

    $order->removeOrderItem($item1);
    expect($order->getTotalPrice())->toBe('5.50');
});
