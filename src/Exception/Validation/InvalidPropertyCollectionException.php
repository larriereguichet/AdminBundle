<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Validation;

use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidPropertyCollectionException extends Exception
{
    public function __construct(
        /* @var ConstraintViolationListInterface[] $errors */
        array $errors,
        ?string $resourceName,
        ?string $operationName,
    ) {
        $message = sprintf(
            'Some properties of the resource "%s" and operation "%s" is not valid :',
            $resourceName,
            $operationName
        );

        /** @var ConstraintViolationInterface|iterable $propertyErrors */
        foreach ($errors as $propertyName => $propertyErrors) {
            $messages = [];

            foreach ($propertyErrors as $propertyError) {
                $messages[] = $propertyError->getPropertyPath().' '.$propertyError->getMessage()->__toString();
            }

            $message .= \PHP_EOL.$propertyName.': '.implode(', ', $messages);
        }

        parent::__construct($message);
    }
}
