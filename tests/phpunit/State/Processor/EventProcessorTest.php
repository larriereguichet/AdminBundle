<?php

namespace LAG\AdminBundle\Tests\State\Processor;

use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\Event\Dispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Event\Events\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Get;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\State\Processor\CompositeProcessor;
use LAG\AdminBundle\State\Processor\EventProcessor;
use LAG\AdminBundle\State\Processor\NormalizationProcessor;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\Tests\TestCase;

class EventProcessorTest extends TestCase
{
    private EventProcessor $processor;
    private ProcessorInterface $decoratedProcessor;
    private ResourceEventDispatcherInterface $eventDispatcher;

    /** @dataProvider operationsProvider */
    public function testProcess(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->myProperty = 'test';
        $resource = new AdminResource(name: 'my_resource');
        $operation = $operation->withResource($resource);

        $this->eventDispatcher
            ->expects($this->exactly(2))
            ->method('dispatchNamedEvents')
            ->willReturnCallback(function (DataEvent $event, array $eventNames, $resourceName, $operationName) use (
                $resource,
                $operation
            ) {
                $assert = false;
                if ($eventNames === [DataEvents::DATA_PROCESS, DataEvents::DATA_RESOURCE_PROCESS, DataEvents::DATA_OPERATION_PROCESS]) {
                    $assert = true;
                }

                if ($eventNames === [DataEvents::DATA_PROCESSED, DataEvents::DATA_RESOURCE_PROCESSED, DataEvents::DATA_OPERATION_PROCESSED]) {
                    $assert = true;
                }
                $this->assertTrue($assert);
                $this->assertCount(3, $eventNames);
                $this->assertEquals($resource->getName(), $resourceName);
                $this->assertEquals($operation->getName(), $operationName);
            })
        ;
        $this->decoratedProcessor
            ->expects($this->once())
            ->method('process')
            ->with($data, $operation, ['id' => 123, ['context' => true]])
        ;

        $this->processor->process($data, $operation, ['id' => 123, ['context' => true]]);
    }

    public static function operationsProvider(): array
    {
        return [
            [new GetCollection()],
            [new Get()],
            [new Create()],
            [new Update()],
            [new Delete()],
        ];
    }

    protected function setUp(): void
    {
        $this->decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->eventDispatcher = $this->createMock(ResourceEventDispatcherInterface::class);
        $this->processor = new EventProcessor(
            $this->decoratedProcessor,
            $this->eventDispatcher,
        );
    }
}
