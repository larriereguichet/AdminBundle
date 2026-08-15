<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Exception\Resource\MissingResourceException;
use LAG\AdminBundle\Metadata\Attribute\Application;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Factory\ApplicationMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itCreatesMetadataWithDefaultValuesFromApplication(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
        );
        $application = new Application(
            name: 'admin',
            translationDomain: 'messages',
            translationPattern: 'admin.{resource}.{message}',
        );

        $collectionFactory = $this->createMock(ResourceCollectionMetadataFactoryInterface::class);
        $collectionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn(['admin.book' => $resource])
        ;
        $applicationFactory = $this->createMock(ApplicationMetadataFactoryInterface::class);
        $applicationFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->with('admin')
            ->willReturn($application)
        ;

        $factory = new ResourceMetadataFactory($collectionFactory, $applicationFactory);
        $result = $factory->createMetadata('admin.book');

        self::assertSame('Books', $result->getTitle());
        self::assertSame('messages', $result->getTranslationDomain());
        self::assertSame('admin.{resource}.{message}', $result->getTranslationPattern());
        self::assertSame('{application}.{resource}.{operation}', $result->getRoutePattern());
        self::assertSame([], $result->getRoles());
    }

    #[Test]
    public function itPreservesResourceTitleWhenAlreadySet(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            title: 'My Books',
        );
        $application = new Application(name: 'admin');

        $collectionFactory = $this->createMock(ResourceCollectionMetadataFactoryInterface::class);
        $collectionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn(['admin.book' => $resource])
        ;
        $applicationFactory = $this->createMock(ApplicationMetadataFactoryInterface::class);
        $applicationFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($application)
        ;

        $factory = new ResourceMetadataFactory($collectionFactory, $applicationFactory);
        $result = $factory->createMetadata('admin.book');

        self::assertSame('My Books', $result->getTitle());
    }

    #[Test]
    public function itThrowsExceptionForMissingResourceClass(): void
    {
        $resource = new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: null,
        );

        $collectionFactory = $this->createMock(ResourceCollectionMetadataFactoryInterface::class);
        $collectionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn(['admin.book' => $resource])
        ;
        $applicationFactory = $this->createMock(ApplicationMetadataFactoryInterface::class);
        $applicationFactory->expects($this->never())->method('createMetadata');

        $factory = new ResourceMetadataFactory($collectionFactory, $applicationFactory);

        $this->expectException(\LAG\AdminBundle\Exception\Exception::class);
        $factory->createMetadata('admin.book');
    }

    #[Test]
    public function itThrowsExceptionForMissingResource(): void
    {
        $collectionFactory = $this->createMock(ResourceCollectionMetadataFactoryInterface::class);
        $collectionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn([])
        ;
        $applicationFactory = $this->createMock(ApplicationMetadataFactoryInterface::class);
        $applicationFactory->expects($this->never())->method('createMetadata');

        $factory = new ResourceMetadataFactory($collectionFactory, $applicationFactory);

        $this->expectException(MissingResourceException::class);
        $factory->createMetadata('admin.missing');
    }
}
