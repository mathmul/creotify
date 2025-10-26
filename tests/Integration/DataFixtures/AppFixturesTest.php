<?php

declare(strict_types=1);

use App\DataFixtures\AppFixtures;
use Doctrine\Persistence\ObjectManager;

it('executes AppFixtures load() successfully', function () {
    self::bootKernel();
    $container = static::getContainer();

    /** @var ObjectManager $manager */
    $manager = $container->get('doctrine')->getManager();

    $fixtures = new AppFixtures();
    $fixtures->load($manager);

    expect($manager)->not->toBeNull();
});
