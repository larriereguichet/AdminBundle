<?php

$finder = PhpCsFixer\Finder::create()
    ->in('config/')
    ->in('src/')
    ->in('tests/phpunit/')
    ->notPath([
        // bug in php-cs-fixer @see https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/issues/8095
        'Resource/Locator/',
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
        'single_line_throw' => false,
    ])
    ->setFinder($finder)
;
