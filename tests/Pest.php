<?php

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

// uses(KernelTestCase::class)->in('Unit');
// uses(WebTestCase::class)->in('Feature');

pest()->extend(KernelTestCase::class)->in('Unit');
pest()->extend(WebTestCase::class)->in('Feature');

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
