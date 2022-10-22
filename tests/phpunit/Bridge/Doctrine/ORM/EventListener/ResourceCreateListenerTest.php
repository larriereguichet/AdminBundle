<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\ResourceCreateListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Event\Events\ResourceCreateEvent;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Property\StringProperty;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ResourceCreateListenerTest extends TestCase
{
    private ResourceCreateListener $listener;
    private MockObject $propertyFactory;

    public function testInvoke(): void
    {
        $property = new StringProperty('a_property');
        $resource = new AdminResource();
        $resource = $resource
            ->withOperations([new Index()])
            ->withDataClass('TestClass')
        ;

        $this
            ->propertyFactory
            ->expects($this->once())
            ->method('createProperties')
            ->with('TestClass')
            ->willReturn([$property])
        ;

        $this->listener->__invoke($event = new ResourceCreateEvent($resource));
        $resource = $event->getResource();

        $this->assertCount(1, $resource->getOperations());
        $this->assertArrayHasKey('index', $resource->getOperations());

        $operation = $resource->getOperations()['index'];
        $this->assertEquals($property, $operation->getProperties()[0]);
    }

    public function testService(): void
    {
        $this->assertServiceExists(ResourceCreateListener::class);
    }

    protected function setUp(): void
    {
        $this->propertyFactory = $this->createMock(MetadataPropertyFactoryInterface::class);
        $this->listener = new ResourceCreateListener($this->propertyFactory);
    }
}
