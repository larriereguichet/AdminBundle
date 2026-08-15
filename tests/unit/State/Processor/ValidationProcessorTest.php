<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\State\Processor;

use LAG\AdminBundle\Exception\InvalidaDataException;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Processor\ValidationProcessor;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidationProcessorTest extends TestCase
{
    private ValidationProcessor $processor;
    private MockObject $decoratedProcessor;
    private MockObject $validator;

    #[Test]
    #[DataProvider('operations')]
    public function itProcessesAnOperation(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->aProperty = 'aValue';

        $operation = $operation->withValidation(true)->withValidationContext(['groups' => ['my_group']]);
        $this->decoratedProcessor
            ->expects($this->once())
            ->method('process')
            ->with($data, $operation, ['my_var' => 'value'], ['test' => 'ok'])
        ;
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($data, [new Valid()], ['groups' => ['my_group']])
            ->willReturn($this->createStub(ConstraintViolationList::class))
        ;

        $this->processor->process($data, $operation, ['my_var' => 'value'], ['test' => 'ok']);
    }

    #[DataProvider('operations')]
    public function testProcessWithoutValidation(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->aProperty = 'aValue';

        $operation = $operation->withValidation(false);
        $this->decoratedProcessor
            ->expects($this->once())
            ->method('process')
            ->with($data, $operation, ['my_var' => 'value'], ['test' => 'ok'])
        ;
        $this->validator
            ->expects($this->never())
            ->method('validate')
        ;

        $this->processor->process($data, $operation, ['my_var' => 'value'], ['test' => 'ok']);
    }

    #[Test]
    public function itThrowsOnValidationErrors(): void
    {
        $data = new \stdClass();
        $operation = (new Create())->withValidation(true)->withValidationContext([]);

        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations->method('count')->willReturn(2);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn($violations)
        ;
        $this->decoratedProcessor->expects($this->never())->method('process');

        $this->expectException(InvalidaDataException::class);
        $this->processor->process($data, $operation, []);
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
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->processor = new ValidationProcessor(
            $this->decoratedProcessor,
            $this->validator,
        );
    }
}
