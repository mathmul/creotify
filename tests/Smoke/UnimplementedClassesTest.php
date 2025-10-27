<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Service\SMS\SmsProviderA;
use App\Service\SMS\SmsProviderB;

it('sends SMS with ProviderA', function () {
    $provider = new SmsProviderA();
    expect(fn () => $provider->sendSMS('+123', 'hi'))->not->toThrow(\Exception::class);
});

it('sends SMS with ProviderB', function () {
    $provider = new SmsProviderB();
    expect(fn () => $provider->sendSMS('+123', 'hi'))->not->toThrow(\Exception::class);
});
