<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\InitializeResourcePropertiesListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Text;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class InitializeResourceListenerTest extends TestCase
{
    private InitializeResourcePropertiesListener $listener;
    private MockObject $propertyFactory;

    public function testInvoke(): void
    {
        $property = new Text('a_property');
        $resource = new Resource();
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

        $this->listener->__invoke($event = new ResourceEvent($resource));
        $resource = $event->getResource();

        $this->assertCount(1, $resource->getOperations());
        $this->assertArrayHasKey('index', $resource->getOperations());
        $operation = $resource->getOperations()['index'];
        $this->assertEquals($property, $operation->getProperties()[0]);
    }

    public function testService(): void
    {
        $this->assertServiceExists(InitializeResourcePropertiesListener::class);
    }

    protected function setUp(): void
    {
        $this->propertyFactory = $this->createMock(MetadataPropertyFactoryInterface::class);
        $this->listener = new InitializeResourcePropertiesListener($this->propertyFactory);
    }
}
