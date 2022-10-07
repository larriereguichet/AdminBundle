<?php

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Tests\TestCase;

class AttributeFactoryTest extends TestCase
{
    public function testCreateResources(): void
    {
        $locator = $this->createLocator();
        $resources = $locator->locateCollection(__DIR__.'/../../Entity');

        foreach ($resources as $resource) {
            $this->assertInstanceOf(AdminResource::class, $resource);
        }
        $this->assertCount(1, $resources);
    }

    private function createLocator(): \LAG\AdminBundle\Metadata\Locator\AttributeLocator
    {
        return new \LAG\AdminBundle\Metadata\Locator\AttributeLocator();
    }
}
