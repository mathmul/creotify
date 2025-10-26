<?php

declare(strict_types=1);

it('responds with 200 OK for /api/health', function () {
    $client = self::getContainer()->get('test.client');
    $client->request('GET', '/api/health');

    expect($client->getResponse()->getStatusCode())->toBe(200)
        ->and($client->getResponse()->getContent())->toBeJson();
});
