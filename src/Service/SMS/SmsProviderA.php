<?php

declare(strict_types=1);

namespace App\Service\SMS;

use App\Service\SMS\Contract\SmsProviderInterface;

final class SmsProviderA implements SmsProviderInterface
{
    public function sendSMS(string $phone, string $message): void
    {
        ray('SMS sent via Provider A', ['phone' => $phone, 'message' => $message]);
    }
}
