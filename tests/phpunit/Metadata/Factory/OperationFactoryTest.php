<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Event\Events\OperationEvent;
use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Factory\OperationFactory;
use LAG\AdminBundle\Metadata\Factory\PropertyFactoryInterface;
use LAG\AdminBundle\Metadata\Filter\Filter;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Metadata\Property\Text;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OperationFactoryTest extends TestCase
{
    private OperationFactory $factory;
    private MockObject $eventDispatcher;
    private MockObject $propertyFactory;
    private MockObject $filterFactory;

    public function testCreate(): void
    {
        $definition = new GetCollection(
            name: 'get_collection',
            properties: [new Text('my_property')],
            filters: [new Filter('my_filter')],
        );
        $resource = new AdminResource(
            name: 'my_resource',
            operations: [$definition],
        );

        $this
            ->eventDispatcher
            ->expects($this->exactly(6))
            ->method('dispatch')
            ->willReturnCallback(function (OperationEvent $event, string $eventName) use ($resource) {
                $this->assertEquals($resource, $event->getOperation()->getResource());
                $this->assertContains($eventName, [
                    OperationEvents::OPERATION_CREATE,
                    OperationEvents::OPERATION_CREATED,
                    'lag_admin.my_resource.operation.create',
                    'lag_admin.my_resource.operation.created',
                    'lag_admin.my_resource.operation.get_collection.create',
                    'lag_admin.my_resource.operation.get_collection.created',
                ]);

                return $event;
            })
        ;

        $this
            ->propertyFactory
            ->expects($this->once())
            ->method('createCollection')
        ;

        $this
            ->filterFactory
            ->expects($this->once())
            ->method('create')
        ;

        $this->factory->create($resource, $definition);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->propertyFactory = $this->createMock(PropertyFactoryInterface::class);
        $this->filterFactory = $this->createMock(FilterFactoryInterface::class);
        $this->factory = new OperationFactory(
            $this->eventDispatcher,
            $this->propertyFactory,
            $this->filterFactory,
        );
    }
}
