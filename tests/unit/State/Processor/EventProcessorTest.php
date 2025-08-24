<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Processor;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Event\DataEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\State\Processor\EventProcessor;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;

final class EventProcessorTest extends TestCase
{
    private EventProcessor $processor;
    private MockObject $decoratedProcessor;
    private MockObject $eventDispatcher;

    #[Test]
    #[DataProvider('operations')]
    public function itProcessesAnOperation(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->myProperty = 'test';
        $resource = new Resource(name: 'my_resource', application: 'my_application');
        $operation = $operation->setResource($resource);

        $this->eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatchBuildEvents')
            ->willReturnMap([
                [
                    new DataEvent($data, $operation),
                    DataEvents::DATA_PROCESS,
                    'my_application',
                    'my_resource',
                    $operation->getFullName(),
                    null,
                ],
                [
                    new DataEvent($data, $operation),
                    DataEvents::DATA_PROCESSED,
                    'my_application',
                    'my_resource',
                    $operation->getFullName(),
                    null,
                ],
            ])
        ;
        $this->decoratedProcessor
            ->expects($this->once())
            ->method('process')
            ->with($data, $operation, ['id' => 123], ['context' => true])
        ;

        $this->processor->process($data, $operation, ['id' => 123], ['context' => true]);
    }

    public static function operations(): array
    {
        return [
            [new Index()],
            [new Show()],
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
