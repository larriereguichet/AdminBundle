<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\Configuration;
use LAG\AdminBundle\Tests\Unit\TestCase;

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
                'request_parameter' => '_application',
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
            'uploads' => [
                'storage' => 'lag_admin.media_storage',
                'media_directory' => '%kernel.project_dir%/public/admin/media/uploads',
            ],
            'applications' => [],
            'resources' => [],
            'grids' => [],
            'cache' => true,
            'grid_templates' => [
                'table' => '@LAGAdmin/grids/table.html.twig',
                'card' => '@LAGAdmin/grids/card.html.twig',
            ],
        ], $data);
    }
}
