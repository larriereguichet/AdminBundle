<?php

namespace LAG\AdminBundle\Tests\Resource\Locator;

use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Resource\Locator\AttributeLocator;
use LAG\AdminBundle\Tests\TestCase;

class AttributeLocatorTest extends TestCase
{
    public function testLocate(): void
    {
        $locator = $this->createLocator();
        $resources = $locator->locate(__DIR__.'/../../Entity');

        foreach ($resources as $resource) {
            $this->assertInstanceOf(Admin::class, $resource);
        }
        $this->assertCount(1, $resources);
    }

    private function createLocator(): AttributeLocator
    {
        return new AttributeLocator();
    }
}
