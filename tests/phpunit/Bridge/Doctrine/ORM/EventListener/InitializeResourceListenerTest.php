<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener\InitializeResourcePropertiesListener;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataPropertyFactoryInterface;
use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Metadata\Property\Text;
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
            ->withOperations([new GetCollection()])
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
        $this->assertArrayHasKey('get_collection', $resource->getOperations());
        $operation = $resource->getOperations()['get_collection'];
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
