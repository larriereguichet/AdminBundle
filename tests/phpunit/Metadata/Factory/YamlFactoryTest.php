<?php

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Metadata\Locator\YamlLocator;
use LAG\AdminBundle\Tests\TestCase;

class YamlFactoryTest extends TestCase
{
    public function testCreateResources(): void
    {
        $locator = $this->createLocator();
        $resources = $locator->locateCollection(__DIR__.'/../../../resources/admin');

        foreach ($resources as $resource) {
            $this->assertInstanceOf(Admin::class, $resource);
        }
        $this->assertCount(1, $resources);
    }

    public function testLocateWithWrongPath(): void
    {
        $this->expectException(Exception::class);
        $locator = $this->createLocator();
        $locator->locateCollection('/wrong/path');
    }

    public function testService(): void
    {
        $this->assertServiceExists(YamlLocator::class);
        $this->assertServiceHasTag(YamlLocator::class, 'lag_admin.resource.locator');
    }

    private function createLocator(): \LAG\AdminBundle\Metadata\Locator\YamlLocator
    {
        return new \LAG\AdminBundle\Metadata\Locator\YamlLocator();
    }    
}
