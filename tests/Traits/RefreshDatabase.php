<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\DataFixtures\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Provides a reliable way to refresh the test database between runs.
 *
 * - Drops all tables
 * - Recreates migration metadata storage
 * - Runs all Doctrine migrations fresh
 *
 * This trait is idempotent and compatible with PostgreSQL, MySQL, and SQLite.
 */
trait RefreshDatabase
{
    protected function refreshDatabase(?AppFixtures $fixtures = null): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        if (empty($metadata)) {
            throw new \RuntimeException('No entity metadata found to build schema.');
        }

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);

        $entityManager->clear();

        $fixtures?->load($entityManager);
    }
}
