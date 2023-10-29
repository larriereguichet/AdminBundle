<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Configuration;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Tests\TestCase;

class ApplicationConfigurationTest extends TestCase
{
    public function testService(): void
    {
        $this->assertServiceExists(ApplicationConfiguration::class);
        $this->assertServiceExists('lag_admin.application');
    }

    public function testDefaultConfiguration(): void
    {
        $configuration = new ApplicationConfiguration([
            'resource_paths' => ['test/'],
        ]);

        $this->assertEquals([
            'name' => 'lag_admin',
            'resource_paths' => ['test/'],
            'title' => 'Admin Bundle',
            'description' => 'Admin Bundle',
            'translation_domain' => 'admin',
            'date_format' => 'medium',
            'time_format' => 'short',
            'date_localization' => true,
            'resource_events' => true,
            'filter_events' => true,
            'grids' => [],
        ], $configuration->toArray());
    }
}
