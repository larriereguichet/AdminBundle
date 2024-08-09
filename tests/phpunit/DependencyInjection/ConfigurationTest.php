<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\DependencyInjection;

use LAG\AdminBundle\DependencyInjection\Configuration;
use LAG\AdminBundle\Resource\Metadata\Image;
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
            'default_application' => 'admin',
            'title' => 'Admin',
            'description' => 'Admin',
            'translation_domain' => 'admin',
            'request' => [
                'application_parameter' => '_application',
                'resource_parameter' => '_resource',
                'operation_parameter' => '_operation',
            ],
            'resource_paths' => [
                '%kernel.project_dir%/config/admin/resources',
                '%kernel.project_dir%/src/Entity',
            ],
            'date_format' => 'medium',
            'time_format' => 'short',
            'date_localization' => true,
            'filter_events' => true,
            'grid_paths' => [
                '%kernel.project_dir%/config/admin/grids',
                '%kernel.project_dir%/src/Entity',
            ],
            'uploads' => ['storage' => 'lag_admin_image.storage'],
            'media_directory' => 'media/images',
            'applications' => []
        ], $data);
    }
}
