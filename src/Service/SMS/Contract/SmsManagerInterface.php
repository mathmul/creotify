<?php

declare(strict_types=1);

namespace App\Service\SMS\Contract;

interface SmsManagerInterface
{
    /**
     * Sends an SMS message to the given recipient.
     *
     * Implementations may apply rate limiting, fallback strategies,
     * or other delivery guarantees.
     */
    public function sendSMS(string $phone, string $message): void;
}
