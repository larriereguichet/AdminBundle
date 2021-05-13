<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src/')
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
        'phpdoc_align' => false,
        'yoda_style' => false,
        'elseif' => true,
        'declare_strict_types' => true,
    ])
    ->setFinder($finder)
;
