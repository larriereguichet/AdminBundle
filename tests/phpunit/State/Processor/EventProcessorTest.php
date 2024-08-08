<?php

namespace LAG\AdminBundle\Tests\State\Processor;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\State\Processor\EventProcessor;
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
        $resource = new Resource(name: 'my_resource');
        $operation = $operation->withResource($resource);

        $this->eventDispatcher
            ->expects($this->exactly(2))
            ->method('dispatchResourceEvents')
            ->willReturnCallback(function (DataEvent $event, array $eventNames, $resourceName, $operationName) use (
                $resource,
                $operation
            ) {
                $assert = false;
                if ($eventNames === [DataEvents::DATA_PROCESS, DataEvents::RESOURCE_DATA_PROCESS, DataEvents::OPERATION_DATA_PROCESS]) {
                    $assert = true;
                }

                if ($eventNames === [DataEvents::DATA_PROCESSED, DataEvents::RESOURCE_DATA_PROCESSED, DataEvents::OPERATION_DATA_PROCESSED]) {
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
            [new Index()],
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
