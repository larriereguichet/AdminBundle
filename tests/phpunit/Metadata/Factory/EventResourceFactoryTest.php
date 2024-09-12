<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Resource\Factory\EventResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

final class EventResourceFactoryTest extends TestCase
{
    private EventResourceFactory $eventResourceFactory;
    private MockObject $eventDispatcher;
    private MockObject $decorated;

    public function testCreate(): void
    {
        $resource = new Resource(
            name: 'my_resource',
            application: 'my_application',
            operations: [new Index()]
        );

        $this->eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatchBuildEvents')
            ->willReturnMap([
                [
                    new ResourceEvent($resource),
                    ResourceEvents::RESOURCE_CREATE_PATTERN,
                    'my_application', 'my_resource',
                    null,
                    null,
                ],
                [
                    new ResourceEvent($resource),
                    ResourceEvents::RESOURCE_CREATED_PATTERN,
                    'my_application',
                    'my_resource',
                    null,
                    null,
                ],
            ])
        ;

        $this->decorated
            ->expects(self::once())
            ->method('create')
            ->with($resource)
            ->willReturn($resource)
        ;

        $this->eventResourceFactory->create($resource);
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(ResourceFactoryInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->eventResourceFactory = new EventResourceFactory(
            $this->eventDispatcher,
            $this->decorated,
        );
    }
}
