<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(['src', 'tests'])
    ->exclude(['var', 'vendor']);

return (new Config())
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'phpdoc_align' => false,
        'phpdoc_summary' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
