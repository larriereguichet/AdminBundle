<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\FrameworkConfig;
use Symfony\Config\WebpackEncoreConfig;

return static function (WebpackEncoreConfig $webpackEncore, FrameworkConfig $framework): void {
    $webpackEncore
        ->outputPath(param('kernel.project_dir').'/public/build')
        ->scriptAttributes('defer', true)
    ;
    $framework->assets()
        ->jsonManifestPath(param('kernel.project_dir').'/public/build/manifest.json')
    ;
};
