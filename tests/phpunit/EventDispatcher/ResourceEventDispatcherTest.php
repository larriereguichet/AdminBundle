<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\EventDispatcher;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcher;
use LAG\AdminBundle\Metadata\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ResourceEventDispatcherTest extends TestCase
{
    private ResourceEventDispatcher $resourceEventDispatcher;
    private MockObject $eventDispatcher;
    private MockObject $buildEventDispatcher;

    #[Test]
    public function itDispatchResourceEvents(): void
    {
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $event = new ResourceEvent($resource);

        $this->eventDispatcher
            ->expects(self::exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function (ResourceEvent $expectedEvent, string $eventName) use ($event): ResourceEvent {
                self::assertEquals($expectedEvent, $event);
                self::assertContains($eventName, [
                    'lag_admin.resource.create',
                    'my_application.resource.create',
                    'my_application.my_resource.create',
                ]);

                return $expectedEvent;
            })
        ;

        $this->resourceEventDispatcher->dispatchEvents($event, ResourceEvents::RESOURCE_CREATE_TEMPLATE);
    }

    #[Test]
    public function itDispatchResourceBuildEvents(): void
    {
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $event = new ResourceEvent($resource);

        $this->buildEventDispatcher
            ->expects(self::exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function (ResourceEvent $expectedEvent, string $eventName) use ($event): ResourceEvent {
                self::assertEquals($expectedEvent, $event);
                self::assertContains($eventName, [
                    'lag_admin.resource.create',
                    'my_application.resource.create',
                    'my_application.my_resource.create',
                ]);

                return $expectedEvent;
            })
        ;

        $this->resourceEventDispatcher->dispatchBuildEvents($event, ResourceEvents::RESOURCE_CREATE_TEMPLATE);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = self::createMock(EventDispatcherInterface::class);
        $this->buildEventDispatcher = self::createMock(EventDispatcherInterface::class);
        $this->resourceEventDispatcher = new ResourceEventDispatcher(
            $this->buildEventDispatcher,
            $this->eventDispatcher,
        );
    }
}
