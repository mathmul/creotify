<?php

declare(strict_types=1);

namespace App\Tests\Traits;

trait TimeTravelHelper
{
    /**
     * Moves the internal time window of a rate-limited manager backward
     * to simulate the passing of time.
     *
     * Example: skipTimeWindow($manager, 'windowStart', 61);
     */
    protected function skipTimeWindow(object $object, string $property, int $seconds = 61): void
    {
        $ref = new \ReflectionProperty($object, $property);
        $ref->setAccessible(true);
        $ref->setValue($object, (new \DateTimeImmutable())->modify("-{$seconds} seconds"));
    }
}
