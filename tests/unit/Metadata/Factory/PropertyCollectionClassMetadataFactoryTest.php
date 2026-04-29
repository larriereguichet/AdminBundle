<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Date;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionClassMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionMetadataFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PropertyCollectionClassMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itReturnsDecoratedPropertiesWhenNonEmpty(): void
    {
        $existingProperty = new Text('title');

        $decorated = $this->createMock(PropertyCollectionMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn(['title' => $existingProperty])
        ;

        $factory = new PropertyCollectionClassMetadataFactory($decorated);
        $properties = $factory->createMetadata(\stdClass::class);

        self::assertSame(['title' => $existingProperty], $properties);
    }

    #[Test]
    public function itCreatesTextPropertiesFromStringFields(): void
    {
        $decorated = $this->createMock(PropertyCollectionMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([])
        ;

        $factory = new PropertyCollectionClassMetadataFactory($decorated);
        $properties = $factory->createMetadata(ClassWithStringProperty::class);

        self::assertCount(1, $properties);
        self::assertInstanceOf(Text::class, current($properties));
        self::assertSame('title', current($properties)->getName());
    }

    #[Test]
    public function itCreatesDatePropertiesFromDateTimeFields(): void
    {
        $decorated = $this->createMock(PropertyCollectionMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([])
        ;

        $factory = new PropertyCollectionClassMetadataFactory($decorated);
        $properties = $factory->createMetadata(ClassWithDateTimeProperty::class);

        self::assertCount(1, $properties);
        self::assertInstanceOf(Date::class, current($properties));
        self::assertSame('publishedAt', current($properties)->getName());
    }

    #[Test]
    public function itSkipsNonScalarNonDateProperties(): void
    {
        $decorated = $this->createMock(PropertyCollectionMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([])
        ;

        $factory = new PropertyCollectionClassMetadataFactory($decorated);
        $properties = $factory->createMetadata(ClassWithMixedProperties::class);

        self::assertCount(1, $properties);
        self::assertSame('name', current($properties)->getName());
    }
}

class ClassWithStringProperty
{
    public string $title = '';
}

class ClassWithDateTimeProperty
{
    public \DateTimeImmutable $publishedAt;
}

class ClassWithMixedProperties
{
    public string $name = '';
    public ?array $tags = null;
    public ?object $relation = null;
}
