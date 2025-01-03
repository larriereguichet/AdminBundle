<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Processor;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\State\Processor\CompositeProcessor;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class CompositeProcessorTest extends TestCase
{
    #[DataProvider('operationsProvider')]
    public function testProcess(OperationInterface $operation): void
    {
        $processor1 = self::createMock(ProcessorInterface::class);
        $processor2 = new FakeProcessor();
        $operation = $operation->withProcessor(FakeProcessor::class)
            ->withResource(new Resource(name: 'my_resource'))
        ;

        $processor1->expects($this->never())
            ->method('process')
        ;

        $processor = new CompositeProcessor([$processor1, $processor2]);
        $processor->process(null, $operation, ['id' => 123, ['context' => true]]);
    }

    #[DataProvider('operationsProvider')]
    public function testProcessWithoutProcessors(OperationInterface $operation): void
    {
        $operation = $operation->withResource(new Resource(name: 'my_resource'));
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'The resource "my_resource" and operation "%s" is not supported by any processor',
            $operation->getName(),
        ));
        $processor = new CompositeProcessor();
        $processor->process(null, $operation);
    }

    public static function operationsProvider(): array
    {
        return [
            [new Index()],
            [new Show()],
            [new Create()],
            [new Update()],
            [new Delete()],
        ];
    }
}
