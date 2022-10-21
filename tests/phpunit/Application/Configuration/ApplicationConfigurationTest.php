<?php

namespace LAG\AdminBundle\Tests\Application\Configuration;

use JK\Configuration\Exception\InvalidConfigurationException;
use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\ActionCollectionField;
use LAG\AdminBundle\Field\ActionField;
use LAG\AdminBundle\Field\ArrayField;
use LAG\AdminBundle\Field\AutoField;
use LAG\AdminBundle\Field\BooleanField;
use LAG\AdminBundle\Field\CountField;
use LAG\AdminBundle\Field\DateField;
use LAG\AdminBundle\Field\LinkField;
use LAG\AdminBundle\Field\MappedField;
use LAG\AdminBundle\Field\StringField;
use LAG\AdminBundle\Metadata\Operation;
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
            'resource_paths' => ['test/'],
            'title' => 'Admin Bundle',
            'description' => 'Admin Bundle',
            'translation_domain' => 'admin',
            'date_format' => 'medium',
            'time_format' => 'short',
            'date_localization' => true,
        ], $configuration->toArray());
    }
}
