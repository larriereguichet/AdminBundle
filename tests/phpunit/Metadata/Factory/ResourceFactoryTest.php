<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Event\Events\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ResourceFactoryTest extends TestCase
{
    private ResourceFactoryInterface $resourceFactory;
    private MockObject $eventDispatcher;
    private MockObject $operationFactory;

    public function testCreate(): void
    {
        $definition = new AdminResource(
            name: 'my_resource',
        );

        $this
            ->eventDispatcher
            ->expects($this->exactly(4))
            ->method('dispatch')
            ->willReturnCallback(function (ResourceEvent $event, string $eventName) use ($definition) {
                if ($eventName === ResourceEvents::RESOURCE_CREATE) {
                    $this->assertEquals($definition, $event->getResource());

                    return $event;
                }

                if ($eventName === 'lag_admin.resource.my_resource.create') {
                    $this->assertEquals($definition, $event->getResource());

                    return $event;
                }

                if ($eventName === ResourceEvents::RESOURCE_CREATED) {
                    $this->assertEquals($definition->getName(), $event->getResource()->getName());

                    return $event;
                }

                if ($eventName === 'lag_admin.resource.my_resource.created') {
                    $this->assertEquals($definition->getName(), $event->getResource()->getName());

                    return $event;
                }
                $this->fail();
            })
        ;

        $this
            ->operationFactory
            ->expects($this->exactly(5))
            ->method('create')
            ->willReturnCallback(function (AdminResource $resource, OperationInterface $operation) use ($definition) {
                $this->assertEquals($resource, $definition);
                $this->assertEquals($resource, $operation->getResource());

                return $operation;
            })
        ;
        $this->resourceFactory->create($definition);
    }

    public function testCreateWithNameChange(): void
    {
        $definition = new AdminResource(
            name: 'my_resource',
        );

        $this
            ->eventDispatcher
            ->expects($this->atLeastOnce())
            ->method('dispatch')
            ->willReturnCallback(function (ResourceEvent $event) {
                $event->setResource($event->getResource()->withName('an_other_name'));

                return $event;
            })
        ;

        $this->expectException(Exception::class);
        $this->resourceFactory->create($definition);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->operationFactory = $this->createMock(OperationFactoryInterface::class);
        $this->resourceFactory = new ResourceFactory($this->eventDispatcher, $this->operationFactory);
    }
}
