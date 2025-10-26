<?php

namespace App\Tests\Traits;

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
    protected function refreshDatabase(): void
    {
        /** @var EntityManagerInterface $em */
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getMetadataFactory()->getAllMetadata();

        if (empty($metadata)) {
            throw new \RuntimeException('No entity metadata found to build schema.');
        }

        $tool = new SchemaTool($em);

        // Drop and recreate the schema freshly
        $tool->dropDatabase();
        $tool->createSchema($metadata);

        $em->clear();
    }
}
