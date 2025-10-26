<?php

declare(strict_types=1);

namespace App\Repository\Contract;

use Doctrine\Persistence\ObjectRepository;

/**
 * @template T of object
 *
 * @extends ObjectRepository<T>
 */
interface RepositoryInterface extends ObjectRepository
{
    /**
     * @param T $entity
     */
    public function save(object $entity, bool $flush = true): void;
}
