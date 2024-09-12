<?php

declare(strict_types=1);

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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class EventProcessorTest extends TestCase
{
    private EventProcessor $processor;
    private ProcessorInterface $decoratedProcessor;
    private ResourceEventDispatcherInterface $eventDispatcher;

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itProcessesAnOperation(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->myProperty = 'test';
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $operation = $operation->withResource($resource);

        $this->eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatchEvents')
            ->willReturnMap([
                [
                    new DataEvent($data, $operation),
                    DataEvents::DATA_PROCESS,
                    'my_application',
                    'my_resource',
                    $operation->getName(),
                    null,
                ],
                [
                    new DataEvent($data, $operation),
                    DataEvents::DATA_PROCESSED,
                    'my_application',
                    'my_resource',
                    $operation->getName(),
                    null,
                ],
            ])
        ;
        $this->decoratedProcessor
            ->expects(self::once())
            ->method('process')
            ->with($data, $operation, ['id' => 123, ['context' => true]])
        ;

        $this->processor->process($data, $operation, ['id' => 123, ['context' => true]]);
    }

    public static function operations(): array
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
        $this->decoratedProcessor = self::createMock(ProcessorInterface::class);
        $this->eventDispatcher = self::createMock(ResourceEventDispatcherInterface::class);
        $this->processor = new EventProcessor(
            $this->decoratedProcessor,
            $this->eventDispatcher,
        );
    }
}
