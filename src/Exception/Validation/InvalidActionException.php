<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Validation;

use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidActionException extends Exception
{
    public function __construct(ConstraintViolationListInterface $errors)
    {
        $message = 'An action is not valid :';

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $message .= \PHP_EOL.$error->getPropertyPath().': '.$error->getMessage();
        }

        parent::__construct($message);
    }
}
