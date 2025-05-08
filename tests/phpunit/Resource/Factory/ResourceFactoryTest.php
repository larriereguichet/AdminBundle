<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Factory;

use LAG\AdminBundle\Event\OperationEvents;
use LAG\AdminBundle\Event\ResourceEventInterface;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\TextFilter;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class ResourceFactoryTest extends TestCase
{
    private ResourceFactoryInterface $resourceFactory;
    private MockObject $definitionFactory;
    private MockObject $eventDispatcher;

    #[Test]
    public function itCreatesAResourceFromADefinition(): void
    {
        $operationDefinition = new Show(shortName: 'my_operation');
        $collectionOperationDefinition = new Index(
            shortName: 'my_collection_operation',
            filters: [new TextFilter(name: 'my_filter')],
        );
        $definition = new Resource(
            name: 'my_resource',
            application: 'my_application',
            operations: [$operationDefinition, $collectionOperationDefinition],
        );

        $this->definitionFactory
            ->expects(self::once())
            ->method('createResourceDefinition')
            ->willReturn($definition)
        ;
        $this->eventDispatcher
            ->expects(self::exactly(6))
            ->method('dispatchBuildEvents')
            ->willReturnCallback(function (ResourceEventInterface $event, string $eventName) {
                self::assertEquals('my_resource', $event->getResource()->getName());
                self::assertContains($eventName, [
                    ResourceEvents::RESOURCE_CREATE_TEMPLATE,
                    ResourceEvents::RESOURCE_CREATED_TEMPLATE,
                    OperationEvents::OPERATION_CREATE_TEMPLATE,
                    OperationEvents::OPERATION_CREATED_TEMPLATE,
                ]);
            })
        ;

        $resource = $this->resourceFactory->create('my_resource');

        self::assertEquals($definition->getName(), $resource->getName());
    }

    protected function setUp(): void
    {
        $this->definitionFactory = self::createMock(DefinitionFactoryInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->resourceFactory = new ResourceFactory(
            $this->definitionFactory,
            $this->eventDispatcher,
        );
    }
}
