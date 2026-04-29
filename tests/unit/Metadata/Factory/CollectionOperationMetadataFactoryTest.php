<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Form\Type\Resource\FilterType;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\TextFilter;
use LAG\AdminBundle\Metadata\Factory\CollectionOperationMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CollectionOperationMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itSetsDefaultFilterFormForCollectionOperation(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new CollectionOperationMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        self::assertSame(FilterType::class, $indexOperation->getFilterForm());
        self::assertSame([], $indexOperation->getCollectionLinks());
        self::assertSame([], $indexOperation->getCollectionFormOptions());
    }

    #[Test]
    public function itDoesNotModifyNonCollectionOperations(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Show()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new CollectionOperationMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $showOperation = current($result->getOperations());
        self::assertInstanceOf(Show::class, $showOperation);
    }

    #[Test]
    public function itPreservesExistingFilterForm(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(filterForm: 'App\Form\MyFilterType')],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new CollectionOperationMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        self::assertSame('App\Form\MyFilterType', $indexOperation->getFilterForm());
    }

    #[Test]
    public function itConfiguresFilterFormOptionsWithFilters(): void
    {
        $filter = new TextFilter('name');
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Index(filters: [$filter])],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new CollectionOperationMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $indexOperation = current($result->getOperations());
        self::assertSame(FilterType::class, $indexOperation->getFilterForm());
        self::assertCount(1, $indexOperation->getFilterFormOptions()['filters']);
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
