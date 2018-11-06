<?php

$finder = \PhpCsFixer\Finder::create()
    ->in('src/')
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
    ])
    ->setFinder($finder)
;
