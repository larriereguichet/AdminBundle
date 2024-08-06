<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Locator;

use LAG\AdminBundle\Resource\Locator\PropertyLocator;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Tests\Fixtures\FakeResource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PropertyLocatorTest extends TestCase
{
    #[Test]
    public function itLocateProperties(): void
    {
        $locator = new PropertyLocator();
        $properties = $locator->locateProperties(new \ReflectionClass(FakeResource::class));

        foreach ($properties as $property) {
            self::assertInstanceOf(PropertyInterface::class, $property);
        }
    }
}
