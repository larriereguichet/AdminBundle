<?php

namespace LAG\AdminBundle\Exception\Validation;

use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidOperationException extends Exception
{
    public function __construct(?string $propertyName, ConstraintViolationListInterface $errors)
    {
        $message = sprintf('The configuration of the operation "%s" is not valid :', $propertyName);

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $message .= PHP_EOL.$error->getPropertyPath().': '.$error->getMessage();
        }

        parent::__construct($message);
    }
}
