<?php

declare(strict_types=1);

use Symfony\Component\Routing\RouterInterface;

it('all public routes respond without error', function () {
    $client = self::getContainer()->get('test.client');
    $router = self::getContainer()->get(RouterInterface::class);

    $skipRoutes = [
        '/api/orders', // requires ?phoneNumber=
    ];

    foreach ($router->getRouteCollection() as $name => $route) {
        $path = $route->getPath();

        // skip admin-only or dynamic routes
        if (
            str_starts_with($path, '/api/admin')
            || str_contains($path, '{')
            || in_array($path, $skipRoutes)
        ) {
            continue;
        }

        $client->request('GET', $path);
        $status = $client->getResponse()->getStatusCode();
        ray($path, $status);

        expect($status)->toBeOneOf([200, 302, 401, 403]);
    }
});
