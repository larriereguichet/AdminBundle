<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Locator;

use LAG\AdminBundle\Resource\Locator\AttributeLocator;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Tests\TestCase;

class AttributeLocatorTest extends TestCase
{
    public function testCreateResources(): void
    {
        $locator = $this->createLocator();
        $resources = $locator->locateCollection(__DIR__.'/../../Entity');

        foreach ($resources as $resource) {
            $this->assertInstanceOf(Resource::class, $resource);
        }
        $this->assertCount(1, $resources);
    }

    private function createLocator(): AttributeLocator
    {
        return new AttributeLocator();
    }
}
