<?php

namespace LAG\AdminBundle\Tests\Resource\Locator;

use LAG\AdminBundle\Resource\Locator\PropertyLocator;
use LAG\AdminBundle\Tests\Fixtures\FakeResource;
use PHPUnit\Framework\TestCase;

class PropertyLocatorTest extends TestCase
{
    private PropertyLocator $propertyLocator;

    public function testLocateProperties(): void
    {
        $properties = $this->propertyLocator->locateMetadata(FakeResource::class);
        $properties = iterator_to_array($properties);

        $this->assertCount(2, $properties);
        $this->assertEquals('id', $properties[0]->getName());
        $this->assertEquals('name', $properties[1]->getName());
    }

    protected function setUp(): void
    {
        $this->propertyLocator = new PropertyLocator();
    }
}
