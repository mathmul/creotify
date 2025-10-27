<?php

declare(strict_types=1);

use App\DataFixtures\AppFixtures;
use App\Tests\Traits\RefreshDatabase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->client = static::createClient();
    $this->refreshDatabase(new AppFixtures());
});

it('creates an order successfully', function () {
    /** @var KernelBrowser $client */
    $client = $this->client;

    $payload = [
        'phoneNumber' => '+38612345678',
        'items' => [
            ['type' => 'article', 'id' => 1],
        ],
    ];

    $client->request('POST', '/api/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));

    expect($client->getResponse()->getStatusCode())->toBe(Response::HTTP_CREATED)
        ->and($client->getResponse()->getContent())->toBeJson();

    $data = json_decode($client->getResponse()->getContent(), true);
    expect($data)->toHaveKeys(['orderNumber', 'customerPhone', 'status', 'totalPrice', 'items'])
        ->and($data['items'][0])->toHaveKeys(['type', 'id', 'price']);
});

it('prevents duplicate subscription purchase', function () {
    /** @var KernelBrowser $client */
    $client = $this->client;

    $payload = [
        'phoneNumber' => '+38600000001',
        'items' => [['type' => 'subscription', 'id' => 1]],
    ];

    // First purchase
    $client->request('POST', '/api/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
    expect($client->getResponse()->getStatusCode())->toBe(Response::HTTP_CREATED);

    // Second purchase (should fail)
    $client->request('POST', '/api/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));

    expect($client->getResponse()->getStatusCode())->toBe(Response::HTTP_BAD_REQUEST)
        ->and(json_decode($client->getResponse()->getContent(), true))
        ->toHaveKey('error');
});

it('lists all orders for a customer', function () {
    /** @var KernelBrowser $client */
    $client = $this->client;

    $payload = [
        'phoneNumber' => '+38622222222',
        'items' => [['type' => 'article', 'id' => 1]],
    ];
    ray('POST /api/orders', $payload);
    // Create order first
    ray('request', $client->request('POST', '/api/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload)));
    ray('response', $client->getResponse());
    expect($client->getResponse()->getStatusCode())->toBe(Response::HTTP_CREATED);

    $em = static::getContainer()->get('doctrine')->getManager();
    $em->flush();
    $em->clear();

    // List orders
    $client->request('GET', '/api/orders?phoneNumber=%2B38622222222'); // "+" URL encoded is "%2B"
    expect($client->getResponse()->getStatusCode())->toBe(Response::HTTP_OK);

    $data = json_decode($client->getResponse()->getContent(), true);
    expect($data)->toBeArray()
        ->and($data[0])->toHaveKeys(['orderNumber', 'status', 'totalPrice', 'createdAt']);
});

it('retrieves a specific order by orderNumber', function () {
    /** @var KernelBrowser $client */
    $client = $this->client;

    // Create new order
    $payload = [
        'phoneNumber' => '+38633333333',
        'items' => [['type' => 'article', 'id' => 1]],
    ];
    $client->request('POST', '/api/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
    $data = json_decode($client->getResponse()->getContent(), true);
    $orderNumber = $data['orderNumber'];

    // Retrieve the order
    $client->request('GET', "/api/orders/{$orderNumber}");
    expect($client->getResponse()->getStatusCode())->toBe(Response::HTTP_OK);

    $order = json_decode($client->getResponse()->getContent(), true);
    expect($order)->toHaveKeys(['orderNumber', 'status', 'totalPrice', 'items']);
});

it('cancels (deletes) an order successfully', function () {
    /** @var KernelBrowser $client */
    $client = $this->client;

    // Create new order
    $payload = [
        'phoneNumber' => '+38644444444',
        'items' => [['type' => 'article', 'id' => 1]],
    ];
    $client->request('POST', '/api/orders', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
    $data = json_decode($client->getResponse()->getContent(), true);
    $orderNumber = $data['orderNumber'];

    // Delete order
    $client->request('DELETE', "/api/orders/{$orderNumber}");

    expect($client->getResponse()->getStatusCode())->toBe(Response::HTTP_NO_CONTENT);

    // Verify it no longer appears in list
    $client->request('GET', '/api/orders?phoneNumber=+38644444444');
    $orders = json_decode($client->getResponse()->getContent(), true);
    expect($orders)->toBeArray()->and($orders)->toBeEmpty();
});
