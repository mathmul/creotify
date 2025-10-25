<?php

declare(strict_types=1);

it('returns 200 OK for /api/health', function () {
    $client = static::createClient();
    $client->request('GET', '/api/health');

    expect($client->getResponse()->getStatusCode())->toBe(200);
});
