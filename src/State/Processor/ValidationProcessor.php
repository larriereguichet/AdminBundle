<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Exception\InvalidaDataException;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ValidationProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private ValidatorInterface $validator,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        if (!$operation->useValidation()) {
            $this->processor->process($data, $operation, $urlVariables, $context);

            return;
        }
        $errors = $this->validator->validate($data, [new Valid()], $operation->getValidationContext());

        if ($errors->count() > 0) {
            throw new InvalidaDataException($errors);
        }
        $this->processor->process($data, $operation, $urlVariables, $context);
    }
}
