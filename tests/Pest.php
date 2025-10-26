<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/*
|--------------------------------------------------------------------------
| Pest Configuration for Symfony
|--------------------------------------------------------------------------
|
| This file boots Symfony's Kernel and provides two base test case classes:
| - KernelTestCase: for unit tests that access the container/services
| - WebTestCase: for functional HTTP tests
|
*/

pest()->extends(KernelTestCase::class)->in('Smoke')->group('smoke');
pest()->extends(KernelTestCase::class)->in('Unit')->group('unit');
pest()->extends(WebTestCase::class)->in('Feature')->group('feature');
pest()->extends(KernelTestCase::class)->in('Integration')->group('integration');

// pest()->printer()->compact(); // switches to dot reporter

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', fn () => $this->toBe(1));
expect()->extend('toBeOneOf', function (array $collection): void {
    $value = $this->value;
    $isOneOf = in_array($value, $collection, true);
    expect($isOneOf)->toBeTrue("Failed asserting that [{$value}] is one of [".implode(', ', $collection).'].');
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
