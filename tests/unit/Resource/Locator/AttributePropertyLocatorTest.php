<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Resource\Locator;

use LAG\AdminBundle\Resource\Locator\AttributePropertyLocator;
use LAG\AdminBundle\Tests\Unit\Fixtures\Book;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AttributePropertyLocatorTest extends TestCase
{
    private AttributePropertyLocator $locator;

    #[Test]
    public function itLocatesAttributesOnClass(): void
    {
        $properties = $this->locator->locateProperties(Book::class);
        $properties = iterator_to_array($properties);

        self::assertCount(4, $properties);

        foreach ($properties as $property) {
            self::assertNotNull($property->getName());
        }
    }

    protected function setUp(): void
    {
        $this->locator = new AttributePropertyLocator();
    }

}
