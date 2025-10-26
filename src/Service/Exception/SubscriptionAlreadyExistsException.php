<?php

declare(strict_types=1);

namespace App\Service\Exception;

final class SubscriptionAlreadyExistsException extends \RuntimeException
{
    public function __construct(string $message = 'Customer already has an active subscription.')
    {
        parent::__construct($message);
    }
}
