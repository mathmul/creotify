<?php

declare(strict_types=1);

use App\DataFixtures\AppFixtures;
use App\Entity\Customer;
use App\Repository\OrderRepository;
use App\Tests\Traits\RefreshDatabase;
use Doctrine\Persistence\ManagerRegistry;

uses(RefreshDatabase::class);

beforeEach(function () {
    self::bootKernel();

    /** @var ManagerRegistry $doctrine */
    $doctrine = static::getContainer()->get('doctrine');

    /* @var OrderRepository $repo */
    $this->repo = static::getContainer()->get(OrderRepository::class);
    $this->doctrine = $doctrine;

    $this->refreshDatabase(new AppFixtures());
});

it('returns bool for customerHasSubscription', function () {
    /** @var Customer|null $customer */
    $customer = $this->doctrine->getRepository(Customer::class)->findOneBy([]);
    expect($customer)->toBeInstanceOf(Customer::class);

    $result = $this->repo->customerHasSubscription($customer);

    expect($result)->toBeBool();
});

it('returns bool for customerHasArticle', function () {
    /** @var Customer|null $customer */
    $customer = $this->doctrine->getRepository(Customer::class)->findOneBy([]);
    expect($customer)->toBeInstanceOf(Customer::class);

    $result = $this->repo->customerHasArticle($customer, 1);

    expect($result)->toBeBool();
});
