<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\EventListener\Admin;

use LAG\AdminBundle\Event\Events\ResourceCreateEvent;
use LAG\AdminBundle\EventListener\Admin\CreateListener;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CreateListenerTest extends TestCase
{
    private CreateListener $listener;
    private MockObject $routeNameGenerator;

    public function testInvoke(): void
    {
        $resource = new AdminResource(
            name: 'test_resource',
            operations: [
                new Index(name: null),
            ]
        );

        $this->listener->__invoke($event = new ResourceCreateEvent($resource));
        $newResource = $event->getResource();

        $this->assertEquals('TestResource', $newResource->getTitle());
        $this->assertTrue($newResource->hasOperation('index'));
        $this->assertCount(1, $newResource->getOperations());

        $index = $newResource->getOperation('index');
        $this->assertEquals('test_resource', $index->getResourceName());
        $this->assertEquals($newResource->getName(), $index->getResource()->getName());
        $this->assertEquals('TestResources', $index->getTitle());
        $this->assertEquals('TestResources', $index->getRoute());
    }

    protected function setUp(): void
    {
        $this->routeNameGenerator = $this->createMock(RouteNameGeneratorInterface::class);
        $this->listener = new CreateListener($this->routeNameGenerator);
    }
}
