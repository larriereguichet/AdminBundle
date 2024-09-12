<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Factory;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Resource\Factory\EventResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EventResourceFactoryTest extends TestCase
{
    private EventResourceFactory $factory;
    private MockObject $eventDispatcher;
    private MockObject $decoratedFactory;

    #[Test]
    public function itCreatesAResourceWithEventsDispatching(): void
    {
        $definition = new Resource(name: 'my_resource', application: 'my_application');

        $this->eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatchBuildEvents')
            ->willReturnCallback(static function (
                ResourceEvent $event,
                string $patternName,
                string $application,
                string $resource,
                ?string $operation,
            ) use ($definition): void {
                self::assertEquals(new ResourceEvent($definition), $event);
                self::assertContains($patternName, [
                    ResourceEvents::RESOURCE_CREATE_PATTERN,
                    ResourceEvents::RESOURCE_CREATED_PATTERN,
                ]);
                self::assertEquals('my_application', $application);
                self::assertEquals('my_resource', $resource);
                self::assertNull($operation);
            })
        ;

        $this->decoratedFactory
            ->expects(self::once())
            ->method('create')
            ->with($definition)
            ->willReturn($definition)
        ;

        $this->factory->create($definition);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->decoratedFactory = self::createMock(ResourceFactoryInterface::class);
        $this->factory = new EventResourceFactory(
            $this->eventDispatcher,
            $this->decoratedFactory,
        );
    }
}
