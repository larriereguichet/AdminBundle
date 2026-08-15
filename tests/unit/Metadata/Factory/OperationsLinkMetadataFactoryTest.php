<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Exception\Operation\UnsupportedLinkConditionException;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\Factory\OperationsLinkMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OperationsLinkMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itAddsDefaultCreateLinkWhenResourceHasCreateOperation(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), new Create()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsLinkMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        $contextualLinks = array_values($indexOperation->getContextualLinks());
        self::assertCount(1, $contextualLinks);
        self::assertSame('admin.book.create', $contextualLinks[0]->getOperation());
    }

    #[Test]
    public function itAddsDefaultUpdateAndDeleteItemLinks(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), new Update(), new Delete()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsLinkMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        $itemLinks = array_values($indexOperation->getItemLinks());
        self::assertCount(2, $itemLinks);
        self::assertSame('admin.book.update', $itemLinks[0]->getOperation());
        self::assertSame('admin.book.delete', $itemLinks[1]->getOperation());
    }

    #[Test]
    public function itDoesNotAddCreateLinkWhenResourceHasNoCreateOperation(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(), new Show()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsLinkMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        self::assertSame([], $indexOperation->getContextualLinks());
    }

    #[Test]
    public function itExpandsShortOperationNamesInCustomLinks(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [
                new Index(contextualLinks: [new Link(operation: 'create')]),
            ],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsLinkMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        $contextualLinks = array_values($indexOperation->getContextualLinks());
        self::assertCount(1, $contextualLinks);
        self::assertSame('admin.book.create', $contextualLinks[0]->getOperation());
    }

    #[Test]
    public function itPreservesFullyQualifiedOperationNames(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [
                new Index(contextualLinks: [new Link(operation: 'other.resource.create')]),
            ],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsLinkMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        $contextualLinks = array_values($indexOperation->getContextualLinks());
        self::assertCount(1, $contextualLinks);
        self::assertSame('other.resource.create', $contextualLinks[0]->getOperation());
    }

    #[Test]
    public function itConvertsStringLinksToLinkObjects(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [
                new Index(contextualLinks: ['admin.book.create']),
            ],
        ));
        $decorated = $this->createStub(ResourceMetadataFactoryInterface::class);
        $decorated->method('createMetadata')->willReturn($resource);

        $factory = new OperationsLinkMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        $contextualLinks = array_values($indexOperation->getContextualLinks());
        self::assertCount(1, $contextualLinks);
        self::assertInstanceOf(Link::class, $contextualLinks[0]);
        self::assertSame('admin.book.create', $contextualLinks[0]->getOperation());
    }

    #[Test]
    public function itRejectsAConditionOnAContextualLink(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(contextualLinks: [new Link(name: 'export', condition: 'data.count() > 0')])],
        ));
        $metadataFactory = $this->createStub(ResourceMetadataFactoryInterface::class);
        $metadataFactory->method('createMetadata')->willReturn($resource);

        $this->expectException(UnsupportedLinkConditionException::class);
        // the message has to say where the condition would work instead, otherwise it only moves the puzzle
        $this->expectExceptionMessage('only evaluated on item links');

        (new OperationsLinkMetadataFactory($metadataFactory))->createMetadata('book');
    }

    #[Test]
    public function itAcceptsAConditionOnAnItemLink(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(itemLinks: [new Link(name: 'archive', condition: 'data.isArchivable()')])],
        ));
        $metadataFactory = $this->createStub(ResourceMetadataFactoryInterface::class);
        $metadataFactory->method('createMetadata')->willReturn($resource);

        $result = (new OperationsLinkMetadataFactory($metadataFactory))->createMetadata('book');

        $operation = current($result->getOperations());
        self::assertSame('data.isArchivable()', current($operation->getItemLinks())->getCondition());
    }

    private function buildResource(Resource $resource): ResourceMetadataInterface
    {
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            $operation->setResource($resource);
            $operations[] = $operation;
        }

        return $resource->withOperations($operations);
    }
}
