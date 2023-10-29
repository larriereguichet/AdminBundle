<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\Configuration;
use LAG\AdminBundle\Metadata\Property\Image;
use LAG\AdminBundle\Tests\TestCase;

class ConfigurationTest extends TestCase
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

        $this->assertEquals([
            'name' => 'lag_admin',
            'title' => 'Admin',
            'description' => 'Admin',
            'translation_domain' => 'admin',
            'resource_paths' => [
                '%kernel.project_dir%/config/admin/resources',
                '%kernel.project_dir%/src/Entity',
            ],
            'date_format' => 'medium',
            'time_format' => 'short',
            'date_localization' => true,
            'filter_events' => true,
            'grids' => [
                'table' => [
                    'template' => '@LAGAdmin/grids/table_grid.html.twig',
                ],
                'card' => [
                    'template' => '@LAGAdmin/grids/card_grid.html.twig',
                    'template_mapping' => [
                        Image::class => '@LAGAdmin/grids/cards/card_image.html.twig',
                    ],
                ],
            ],
        ], $data);
    }
}
