<?php

$finder = \PhpCsFixer\Finder::create()
    ->in('src/')
    ->in('tests/AdminBundle')
    ->exclude([
        'bin',
        'build',
        'vendor',
    ])
;

return \PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'phpdoc_align' => false,
        'yoda_style' => false,
        'elseif' => true,
    ])
    ->setFinder($finder)
;
