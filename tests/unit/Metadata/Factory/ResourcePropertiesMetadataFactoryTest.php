<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Factory\PropertyCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourcePropertiesMetadataFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourcePropertiesMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itCreatesMetadataWithDefaultLabelsAndSortingPath(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
        );
        $property = new Text('published_at');

        $resourceFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $resourceFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->with('book')
            ->willReturn($resource)
        ;

        $propertiesFactory = $this->createMock(PropertyCollectionMetadataFactoryInterface::class);
        $propertiesFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->with(\stdClass::class)
            ->willReturn([$property])
        ;

        $factory = new ResourcePropertiesMetadataFactory($resourceFactory, $propertiesFactory);
        $metadata = $factory->createMetadata('book');

        self::assertCount(1, $metadata->getProperties());

        $createdProperty = $metadata->getProperties()['published_at'];
        self::assertSame('Published at', $createdProperty->getLabel());
        self::assertSame('published_at', $createdProperty->getPropertyPath());
        self::assertSame('published_at', $createdProperty->getSortingPath());
    }

    #[Test]
    public function itDoesNotLeakTheSortingPathToTheNextProperty(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
        );
        $resourceFactory = $this->createStub(ResourceMetadataFactoryInterface::class);
        $resourceFactory->method('createMetadata')->willReturn($resource);

        $propertiesFactory = $this->createStub(PropertyCollectionMetadataFactoryInterface::class);
        $propertiesFactory->method('createMetadata')->willReturn([
            new Text('title'),
            new Text('summary', sortable: false),
        ]);

        $factory = new ResourcePropertiesMetadataFactory($resourceFactory, $propertiesFactory);
        $properties = $factory->createMetadata('book')->getProperties();

        self::assertSame('title', $properties['title']->getSortingPath());
        self::assertNull($properties['summary']->getSortingPath());
    }

    #[Test]
    public function itUsesTranslationPatternWhenProvided(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            translationPattern: 'lag_admin.{application}.{resource}.{message}',
        );
        $property = new Text('published_at', sortable: false);

        $resourceFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $resourceFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $propertiesFactory = $this->createMock(PropertyCollectionMetadataFactoryInterface::class);
        $propertiesFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([$property])
        ;

        $factory = new ResourcePropertiesMetadataFactory($resourceFactory, $propertiesFactory);
        $metadata = $factory->createMetadata('book');

        self::assertSame('lag_admin.admin.book.published_at', $metadata->getProperties()['published_at']->getLabel());
    }

    #[Test]
    public function itUsesPropertyPathAsSortingPath(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
        );
        $property = new Text('name', propertyPath: 'nested.name');

        $resourceFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $resourceFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $propertiesFactory = $this->createMock(PropertyCollectionMetadataFactoryInterface::class);
        $propertiesFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([$property])
        ;

        $factory = new ResourcePropertiesMetadataFactory($resourceFactory, $propertiesFactory);
        $metadata = $factory->createMetadata('book');

        self::assertSame('nested.name', $metadata->getProperties()['name']->getSortingPath());
    }

    #[Test]
    public function itConfiguresLinkPropertyTextAndOperation(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
        );

        $link = $this->getMockBuilder(Link::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getName',
                'getLabel',
                'isSortable',
                'getSortingPath',
                'getPropertyPath',
                'getText',
                'getOperation',
                'withLabel',
                'withPropertyPath',
                'withSortingPath',
                'withText',
                'withOperation',
            ])
            ->getMock()
        ;

        $link->method('getName')->willReturn('details');
        $link->method('getLabel')->willReturn(null);
        $link->method('isSortable')->willReturn(false);
        $link->method('getSortingPath')->willReturn(null);
        $link->method('getPropertyPath')->willReturn(null);
        $link->method('getText')->willReturn(null);
        $link->method('getOperation')->willReturn(null);

        $resourceFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $resourceFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $propertiesFactory = $this->createMock(PropertyCollectionMetadataFactoryInterface::class);
        $propertiesFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([$link])
        ;

        $linkWithLabel = clone $link;
        $linkWithPropertyPath = clone $link;
        $linkWithSortingPath = clone $link;
        $linkWithText = clone $link;
        $linkWithOperation = clone $link;

        $link
            ->expects($this->once())
            ->method('withLabel')
            ->with('Details')
            ->willReturn($linkWithLabel)
        ;
        $linkWithLabel
            ->expects($this->once())
            ->method('withPropertyPath')
            ->with('details')
            ->willReturn($linkWithPropertyPath)
        ;
        $linkWithPropertyPath
            ->expects($this->once())
            ->method('withSortingPath')
            ->with(null)
            ->willReturn($linkWithSortingPath)
        ;
        $linkWithSortingPath
            ->expects($this->once())
            ->method('withText')
            ->with('details')
            ->willReturn($linkWithText)
        ;
        $linkWithText
            ->expects($this->once())
            ->method('withOperation')
            ->with('admin.book.')
            ->willReturn($linkWithOperation)
        ;
        $linkWithOperation
            ->expects($this->once())
            ->method('withPropertyPath')
            ->with('details')
            ->willReturn($linkWithOperation)
        ;
        $linkWithOperation
            ->method('getName')
            ->willReturn('details')
        ;

        $factory = new ResourcePropertiesMetadataFactory($resourceFactory, $propertiesFactory);
        $metadata = $factory->createMetadata('book');

        self::assertCount(1, $metadata->getProperties());
        self::assertSame($linkWithOperation, $metadata->getProperties()['details']);
    }
}
