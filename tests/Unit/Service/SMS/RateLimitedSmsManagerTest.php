<?php

declare(strict_types=1);

use App\Service\SMS\Contract\SmsManagerInterface;
use App\Service\SMS\Contract\SmsProviderInterface;
use App\Service\SMS\RateLimitedSmsManager;
use App\Tests\Traits\TimeTravelHelper;

uses(TimeTravelHelper::class);

beforeEach(function () {
    $this->primary = Mockery::mock(SmsProviderInterface::class);
    $this->fallback = Mockery::mock(SmsProviderInterface::class);
});

it('uses primary provider until rate limit is reached', function () {
    $this->expectNotToPerformAssertions();

    /** @var SmsManagerInterface $manager */
    $manager = new RateLimitedSmsManager($this->primary, $this->fallback, 5);

    $this->primary->shouldReceive('sendSMS')->times(5);
    $this->fallback->shouldNotReceive('sendSMS');

    for ($i = 1; $i <= 5; ++$i) {
        $manager->sendSMS('+123', "message {$i}");
    }
});

it('switches to fallback provider after 5 messages', function () {
    $this->expectNotToPerformAssertions();

    /** @var SmsManagerInterface $manager */
    $manager = new RateLimitedSmsManager($this->primary, $this->fallback, 5);

    $this->primary->shouldReceive('sendSMS')->times(5);
    $this->fallback->shouldReceive('sendSMS')->once();

    for ($i = 1; $i <= 6; ++$i) {
        $manager->sendSMS('+123', "message {$i}");
    }
});

it('resets counter after a minute and uses primary again', function () {
    $this->expectNotToPerformAssertions();

    /** @var SmsManagerInterface $manager */
    $manager = new RateLimitedSmsManager($this->primary, $this->fallback, 5);

    $this->primary->shouldReceive('sendSMS')->times(6); // 5 + 1 after reset
    $this->fallback->shouldReceive('sendSMS')->once();

    for ($i = 1; $i <= 6; ++$i) {
        $manager->sendSMS('+123', "message {$i}");
    }

    $this->skipTimeWindow($manager, 'windowStart'); // skip 1 minute

    $manager->sendSMS('+123', 'after-reset');
});
