<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('env(DATABASE_URL)', '');

    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'driver' => 'pdo_mysql',
            'server_version' => 'mariadb-10.3.22',
            'url' => '%env(DATABASE_URL)%',
        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'mappings' => [
                'App' => [
                    'is_bundle' => false,
                    'type' => 'attribute',
                    'dir' => '%kernel.project_dir%/src/Entity',
                    'prefix' => 'App\Entity',
                    'alias' => 'App',
                ],
            ],
        ],
    ]);
};
