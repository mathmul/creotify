<?php

declare(strict_types=1);

namespace App\Service\Exception;

final class DuplicatePurchaseException extends \RuntimeException
{
    public function __construct(string $message = 'Customer has already purchased this article.')
    {
        parent::__construct($message);
    }
}
