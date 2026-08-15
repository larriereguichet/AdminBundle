<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\Configuration;
use LAG\AdminBundle\Tests\Unit\TestCase;
use Symfony\Component\Config\Definition\Processor;

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
            'mapping' => [
                'paths' => [
                    '%kernel.project_dir%/src/Entity',
                ],
            ],
            'applications' => [],
            'uploads' => [
                'storage' => 'lag_admin.media_storage',
                'media_directory' => '%kernel.project_dir%/public/admin/media/uploads',
            ],
            'request_parameter' => '_lag_operation',
            'date_format' => 'medium',
            'time_format' => 'short',
            'date_localization' => true,
            'filter_events' => true,
            'cache' => true,
            'grid_templates' => [
                'table' => '@LAGAdmin/components/table_grid.html.twig',
                'card' => '@LAGAdmin/components/card_grid.html.twig',
            ],
        ], $data);
    }

    public function testApplicationDefaults(): void
    {
        $data = (new Processor())->processConfiguration(new Configuration(), [
            ['applications' => ['admin' => []]],
        ]);

        self::assertSame([
            'date_format' => 'medium',
            'time_format' => 'short',
            'translation_domain' => 'messages',
            'translation_pattern' => '{application}.{resource}.{message}',
            'route_pattern' => '{application}.{resource}.{operation}',
            'base_template' => '@LAGAdmin/base.html.twig',
        ], $data['applications']['admin']);
    }
}
