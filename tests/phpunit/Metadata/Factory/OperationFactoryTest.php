<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Resource\Factory\OperationFactory;
use LAG\AdminBundle\Resource\Metadata\Filter;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Text;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OperationFactoryTest extends TestCase
{
    private OperationFactory $factory;
    private MockObject $filterFactory;

    public function testCreate(): void
    {
        $definition = new Index(
            name: 'index',
            filters: [new Filter('my_filter')],
        );
        $resource = new Resource(
            name: 'my_resource',
            operations: [$definition],
            properties: [new Text('my_property')],
        );

        $this
            ->filterFactory
            ->expects($this->once())
            ->method('create')
        ;

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
