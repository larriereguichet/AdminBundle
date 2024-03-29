<?php

$finder = PhpCsFixer\Finder::create()
    ->in('config/')
    ->in('src/')
    ->in('tests/phpunit/')
    ->exclude([
        'bin',
        'build',
        'vendor',
    ])
;
$config = new PhpCsFixer\Config();

return $config
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'no_trailing_comma_in_singleline' => true,
        'trailing_comma_in_multiline' => true,
        'phpdoc_align' => false,
        'yoda_style' => false,
        'elseif' => true,
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
;
