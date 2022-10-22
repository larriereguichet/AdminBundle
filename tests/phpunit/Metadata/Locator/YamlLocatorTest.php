<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Locator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Locator\YamlLocator;
use LAG\AdminBundle\Tests\TestCase;

class YamlLocatorTest extends TestCase
{
    public function testCreateResources(): void
    {
        $locator = $this->createLocator();
        $resources = $locator->locateCollection(__DIR__.'/../../../app/config/resources/admin');

        foreach ($resources as $resource) {
            $this->assertInstanceOf(AdminResource::class, $resource);
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

    private function createLocator(): YamlLocator
    {
        return new YamlLocator();
    }
}
