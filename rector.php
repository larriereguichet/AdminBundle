<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withSkipPath(__DIR__.'/tests/app')
    ->withPhpSets(php83: true)
    ->withTypeCoverageLevel(0)

    ->withSkip([
        AddOverrideAttributeToOverriddenMethodsRector::class,
    ])
;
