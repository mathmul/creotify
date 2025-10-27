<?php

declare(strict_types=1);

namespace App\Service\SMS;

use App\Service\SMS\Contract\SmsManagerInterface;
use App\Service\SMS\Contract\SmsProviderInterface;

final class RateLimitedSmsManager implements SmsManagerInterface
{
    private int $sentCount = 0;
    private \DateTimeImmutable $windowStart;

    public function __construct(
        private readonly SmsProviderInterface $primary,
        private readonly SmsProviderInterface $fallback,
        private readonly int $limitPerMinute = 5,
    ) {
        $this->windowStart = new \DateTimeImmutable();
    }

    public function sendSMS(string $to, string $content): void
    {
        $now = new \DateTimeImmutable();

        if ($now->getTimestamp() - $this->windowStart->getTimestamp() >= 60) {
            $this->sentCount = 0;
            $this->windowStart = $now;
        }

        if ($this->sentCount < $this->limitPerMinute) {
            $this->primary->sendSMS($to, $content);
            ++$this->sentCount;
        } else {
            $this->fallback->sendSMS($to, $content);
        }
    }
}
