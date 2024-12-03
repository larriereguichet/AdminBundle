<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Processor;

use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\Session\FlashMessageHelperInterface;
use LAG\AdminBundle\State\Processor\FlashMessageProcessor;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\Tests\Fixtures\FakeResource;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class FlashMessageProcessorTest extends TestCase
{
    private FlashMessageProcessor $processor;
    private MockObject $decoratedProcessor;
    private MockObject $flashMessageHelper;

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itProcessSuccessMessage(OperationInterface $operation): void
    {
        $resource = new FakeResource();
        $operation = $operation->withSuccessMessage('some_success_message');

        $this->decoratedProcessor
            ->expects(self::once())
            ->method('process')
            ->with($resource, $operation)
        ;
        $this->flashMessageHelper
            ->expects(self::once())
            ->method('success')
            ->with('some_success_message')
        ;

        $this->processor->process($resource, $operation);
    }

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itDoesNotProcessEmptyMessage(OperationInterface $operation): void
    {
        $resource = new FakeResource();

        $this->decoratedProcessor
            ->expects(self::once())
            ->method('process')
            ->with($resource, $operation)
        ;
        $this->flashMessageHelper
            ->expects(self::never())
            ->method('success')
        ;

        $this->processor->process($resource, $operation);
    }

    public static function operations(): iterable
    {
        yield [new Index()];
        yield [new Show()];
        yield [new Create()];
        yield [new Update()];
        yield [new Delete()];
    }

    protected function setUp(): void
    {
        $this->decoratedProcessor = self::createMock(ProcessorInterface::class);
        $this->flashMessageHelper = self::createMock(FlashMessageHelperInterface::class);
        $this->processor = new FlashMessageProcessor(
            $this->decoratedProcessor,
            $this->flashMessageHelper,
        );
    }
}
