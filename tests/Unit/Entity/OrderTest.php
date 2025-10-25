<?php

use App\Entity\Order;
use App\Entity\Customer;
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
