<?php

declare(strict_types=1);

use App\Entity\Customer;
use App\Entity\Order;

it('adds and removes orders', function () {
    $customer = new Customer('123');
    $order = new Order($customer);

    $customer->addOrder($order);
    expect($customer->getOrders())->toHaveCount(1)
        ->and($order->getCustomer())->toBe($customer);

    $customer->removeOrder($order);
    expect($customer->getOrders())->toHaveCount(0);
});
