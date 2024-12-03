<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactory;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Resource\Metadata\Filter;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class OperationFactoryTest extends TestCase
{
    private OperationFactoryInterface $factory;
    private MockObject $filterFactory;

    #[Test]
    public function itCreatesAnOperation(): void
    {
        $resource = new Resource(name: 'my_resource');
        $definition = (new Index(
            name: 'index',
            filters: [new Filter('my_filter')],
        ))->withResource($resource);

        $this->filterFactory
            ->expects(self::once())
            ->method('create')
            ->with($definition, new Filter('my_filter'))
            ->willReturn(new Filter('my_filter'))
        ;

        $operation = $this->factory->create($definition);

        self::assertEquals($resource, $operation->getResource());
        self::assertInstanceOf(Index::class, $operation);
        self::assertCount(1, $operation->getFilters());
    }

    #[Test]
    public function itDoesNotCreateAnOperationWithoutResource(): void
    {
        $definition = new Index(name: 'my_operation');

        self::expectExceptionObject(new Exception('The operation should be owned by a resource'));
        $this->factory->create($definition);
    }

    protected function setUp(): void
    {
        $this->filterFactory = $this->createMock(FilterFactoryInterface::class);
        $this->factory = new OperationFactory(
            $this->filterFactory,
        );
    }
}
