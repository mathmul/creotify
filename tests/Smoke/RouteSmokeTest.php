<?php

use Symfony\Component\Routing\RouterInterface;

it('all public routes respond without error', function () {
    $client = self::getContainer()->get('test.client');
    $router = self::getContainer()->get(RouterInterface::class);

    foreach ($router->getRouteCollection() as $name => $route) {
        $path = $route->getPath();

        // skip admin-only or dynamic routes
        if (str_starts_with($path, '/api/admin') || str_contains($path, '{')) {
            continue;
        }

        $client->request('GET', $path);
        $status = $client->getResponse()->getStatusCode();

        expect($status)->toBeOneOf([200, 302, 401, 403]);
    }
});
