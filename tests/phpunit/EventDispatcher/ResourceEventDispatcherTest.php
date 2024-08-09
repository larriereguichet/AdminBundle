<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\EventDispatcher;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcher;
use LAG\AdminBundle\Resource\Metadata\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ResourceEventDispatcherTest extends TestCase
{
    private ResourceEventDispatcher $resourceEventDispatcher;
    private MockObject $eventDispatcher;

    #[Test]
    public function itDispatchEvents(): void
    {
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $event = new ResourceEvent($resource);

        $this->eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (ResourceEvent $expectedEvent, string $eventName) use ($event): ResourceEvent {
                self::assertEquals($expectedEvent, $event);
                self::assertContains($eventName, [
                    'lag_admin.resource.create',
                    'my_application.my_resource.create',
                ]);

                return $expectedEvent;
            })
        ;

        $this->resourceEventDispatcher->dispatchResourceEvents(
            $event,
            ResourceEvents::RESOURCE_CREATE,
            'my_application',
            'my_resource',
        );
    }

    protected function setUp(): void
    {
        $this->eventDispatcher = self::createMock(EventDispatcherInterface::class);
        $this->resourceEventDispatcher = new ResourceEventDispatcher($this->eventDispatcher);
    }
}
