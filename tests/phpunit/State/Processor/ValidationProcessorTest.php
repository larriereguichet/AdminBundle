<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Processor;

use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Processor\ValidationProcessor;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationProcessorTest extends TestCase
{
    private ValidationProcessor $processor;
    private MockObject $decoratedProcessor;
    private MockObject $validator;

    #[Test]
    #[DataProvider(methodName: 'operations')]
    public function itProcessesAnOperation(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->aProperty = 'aValue';

        $operation = $operation->withValidation(true)->withValidationContext(['groups' => ['my_group']]);
        $this->decoratedProcessor
            ->expects(self::once())
            ->method('process')
            ->with($data, $operation, ['my_var' => 'value'], ['test' => 'ok'])
        ;
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with($data, [new Valid()], ['groups' => ['my_group']])
            ->willReturn(self::createMock(ConstraintViolationList::class))
        ;

        $this->processor->process($data, $operation, ['my_var' => 'value'], ['test' => 'ok']);
    }

    /** @dataProvider operations */
    public function testProcessWithoutValidation(OperationInterface $operation): void
    {
        $data = new \stdClass();
        $data->aProperty = 'aValue';

        $operation = $operation->withValidation(false);
        $this->decoratedProcessor
            ->expects(self::once())
            ->method('process')
            ->with($data, $operation, ['my_var' => 'value'], ['test' => 'ok'])
        ;
        $this->validator
            ->expects($this->never())
            ->method('validate')
        ;

        $this->processor->process($data, $operation, ['my_var' => 'value'], ['test' => 'ok']);
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
        $this->validator = self::createMock(ValidatorInterface::class);
        $this->processor = new ValidationProcessor(
            $this->decoratedProcessor,
            $this->validator,
        );
    }
}
