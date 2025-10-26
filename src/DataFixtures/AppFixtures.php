<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\SubscriptionPackage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // --- 10 Articles ---
        $articles = [];
        for ($i = 0; $i < 10; ++$i) {
            $article = new Article();
            $article->setName($faker->words(3, true))
                ->setDescription($faker->sentence(10))
                ->setPrice((string) $faker->randomFloat(2, 5, 100))
                ->setSupplierEmail($faker->companyEmail());
            $manager->persist($article);
            $articles[] = $article;
        }

        // --- 3 Subscription Packages ---
        $packages = [];
        for ($i = 0; $i < 3; ++$i) {
            $package = new SubscriptionPackage();
            $package->setName($faker->words(2, true))
                ->setDescription($faker->sentence(8))
                ->setPrice((string) $faker->randomFloat(2, 10, 50))
                ->setIncludesMagazine($faker->boolean(70));
            $manager->persist($package);
            $packages[] = $package;
        }

        // --- 5 Customers + 1–2 Orders per Customer + 1–3 OrderItems per Order ---
        for ($i = 0; $i < 5; ++$i) {
            $customerPhone = $faker->unique()->numerify('+386########');
            $customer = new Customer($customerPhone);

            $orderCount = $faker->numberBetween(1, 2);
            for ($j = 0; $j < $orderCount; ++$j) {
                $order = new Order($customer);

                $itemCount = $faker->numberBetween(1, 3);
                for ($k = 0; $k < $itemCount; ++$k) {
                    if ($faker->boolean(80)) {
                        // OrderItem = Article
                        $article = $faker->randomElement($articles);
                        $item = new OrderItem(
                            $order,
                            'article',
                            $article->getId() ?? $faker->numberBetween(1, 999),
                            $article->getPrice()
                        );
                    } else {
                        // OrderItem = Subscription Package
                        $package = $faker->randomElement($packages);
                        $item = new OrderItem(
                            $order,
                            'subscription',
                            $package->getId() ?? $faker->numberBetween(1, 999),
                            $package->getPrice()
                        );
                    }
                    $order->addOrderItem($item);
                    $manager->persist($item);
                }

                $order->setTotalPrice(
                    (string) array_sum(array_map(fn (OrderItem $oi) => (float) $oi->getPrice(), $order->getOrderItems()->toArray()))
                );

                $manager->persist($order);
            }

            $manager->persist($customer);
        }

        $manager->flush();
    }
}
