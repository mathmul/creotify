<?php

declare(strict_types=1);

namespace App\Tests\Traits;

trait EntityHelper
{
    protected function setEntityId(object $entity, int $id): void
    {
        $ref = new \ReflectionProperty($entity, 'id');
        $ref->setAccessible(true);
        $ref->setValue($entity, $id);
    }
}
