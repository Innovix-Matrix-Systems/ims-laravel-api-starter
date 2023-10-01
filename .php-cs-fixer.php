<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude(['bootstrap', 'storage', 'vendor', 'docker'])
    ->name('*.php')
    ->name('_ide_helper')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'not_operator_with_successor_space' => true,
        'ordered_imports' => true,
        'phpdoc_align' => true,
        // Add more rules as needed
    ])
    ->setUsingCache(false)
    ->setLineEnding(PHP_EOL)
    ->setFinder($finder);
