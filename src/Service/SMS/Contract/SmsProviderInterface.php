<?php

declare(strict_types=1);

namespace App\Service\SMS\Contract;

interface SmsProviderInterface
{
    public function sendSMS(string $phone, string $message): void;
}
