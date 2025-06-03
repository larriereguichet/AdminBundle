<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\Configuration;
use LAG\AdminBundle\Tests\TestCase;

final class ConfigurationTest extends TestCase
{
    /**
     * GetConfigTreeBuilder method should a return valid array nodes. The configuration is more tested in
     * LagAdminExtensionTest.
     */
    public function testGetConfigTreeBuilder(): void
    {
        $configuration = new Configuration();
        $tree = $configuration->getConfigTreeBuilder();
        $data = $tree->buildTree()->finalize([]);

        self::assertEquals([
            'request' => [
                'application_parameter' => '_application',
                'resource_parameter' => '_resource',
                'operation_parameter' => '_operation',
            ],
            'mapping' => [
                'paths' => [
                    '%kernel.project_dir%/src/Entity',
                ],
            ],
            'date_format' => 'medium',
            'time_format' => 'short',
            'date_localization' => true,
            'filter_events' => true,
            'uploads' => ['storage' => 'lag_admin_image.storage'],
            'media_directory' => 'media/images',
            'applications' => [],
            'resources' => [],
            'grids' => [],
            'cache' => true,
        ], $data);
    }
}
