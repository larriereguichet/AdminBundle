<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventDispatcher;

use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcher;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ResourceEventDispatcherTest extends TestCase
{
    private ResourceEventDispatcher $resourceEventDispatcher;
    private MockObject $eventDispatcher;

    #[Test]
    public function itDispatchResourceEvents(): void
    {
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $event = new ResourceEvent($resource);

        $this->eventDispatcher
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->willReturnCallback(function (ResourceEvent $expectedEvent, string $eventName) use ($event): ResourceEvent {
                self::assertEquals($expectedEvent, $event);
                self::assertContains($eventName, [
                    'lag_admin.resource.controller',
                    'my_application.resource.controller',
                    'my_application.my_resource.controller',
                ]);

                return $expectedEvent;
            })
        ;

        $this->resourceEventDispatcher->dispatchEvents($event, ResourceControllerEvents::RESOURCE_CONTROLLER);
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->resourceEventDispatcher = new ResourceEventDispatcher(
            $this->eventDispatcher,
        );
    }
}
