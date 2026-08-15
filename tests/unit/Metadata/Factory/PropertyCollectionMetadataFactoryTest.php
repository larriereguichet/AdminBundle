<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionMetadataFactory;
use LAG\AdminBundle\Metadata\PropertyMetadataInterface;
use LAG\AdminBundle\Tests\Application\Entity\Book;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PropertyCollectionMetadataFactoryTest extends TestCase
{
    private PropertyCollectionMetadataFactory $factory;

    #[Test]
    public function itCreatesPropertiesMetadataFromAttributes(): void
    {
        $properties = $this->factory->createMetadata(Book::class);

        $this->assertCount(4, $properties);
        $this->assertEquals($properties['id'], new Link(name: 'id', propertyPath: true, label: false, operation: 'show', textPath: 'id'));
        $this->assertEquals($properties['show'], new Link(name: 'show', propertyPath: true, label: 'actions', operation: 'show', text: 'Show book'));
        $this->assertEquals($properties['name'], new Text(name: 'name'));
        $this->assertEquals($properties['isbn'], new Text(name: 'isbn'));
    }

    #[Test]
    public function itCreatesPropertiesFromClassLevelAttributes(): void
    {
        $properties = $this->factory->createMetadata(ClassWithClassLevelProperty::class);

        self::assertNotEmpty($properties);
        self::assertInstanceOf(PropertyMetadataInterface::class, current($properties));
        self::assertSame('summary', current($properties)->getName());
    }

    protected function setUp(): void
    {
        $this->factory = new PropertyCollectionMetadataFactory();
    }
}

#[Text('summary')]
class ClassWithClassLevelProperty
{
}
