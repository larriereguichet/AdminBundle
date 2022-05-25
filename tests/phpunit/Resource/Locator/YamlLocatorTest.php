<?php

namespace LAG\AdminBundle\Tests\Resource\Locator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Resource\Locator\YamlLocator;
use LAG\AdminBundle\Tests\TestCase;

class YamlLocatorTest extends TestCase
{
    public function testLocate(): void
    {
        $locator = $this->createLocator();
        $resources = $locator->locate(__DIR__.'/../../../resources/admin');

        foreach ($resources as $resource) {
            $this->assertInstanceOf(Admin::class, $resource);
        }
        $this->assertCount(1, $resources);
    }

    public function testLocateWithWrongPath(): void
    {
        $this->expectException(Exception::class);
        $locator = $this->createLocator();
        $locator->locate('/wrong/path');
    }

    public function testService(): void
    {
        $this->assertServiceExists(YamlLocator::class);
        $this->assertServiceHasTag(YamlLocator::class, 'lag_admin.resource.locator');
    }

    private function createLocator(): YamlLocator
    {
        return new YamlLocator();
    }    
}
