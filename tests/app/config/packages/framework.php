<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'secret' => 'test',
        'test' => null,
        'form' => null,
        'csrf_protection' => false,
        'validation' => [
            'enabled' => false,
        ],
        'assets' => ['json_manifest_path' => '%kernel.project_dir%/public/build/manifest.json'],
    ]);
};
