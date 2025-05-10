<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\Filter;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Resource\Factory\OperationFactory;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class OperationFactoryTest extends TestCase
{
    private OperationFactory $factory;
    private MockObject $filterFactory;

    #[Test]
    public function itCreatesAnOperation(): void
    {
        $resource = new Resource(
            name: 'my_resource',
            properties: [new Text('my_property')],
        );
        $definition = (new Index(
            name: 'index',
            filters: [new Filter('my_filter')],
        ))->withResource($resource);

        $this->filterFactory
            ->expects(self::once())
            ->method('create')
        ;

        $this->factory->create($definition);
    }

    protected function setUp(): void
    {
        $this->filterFactory = self::createMock(FilterFactoryInterface::class);
        $this->factory = new OperationFactory(
            $this->filterFactory,
        );
    }
}
