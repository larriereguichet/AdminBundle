<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidFilterException extends Exception
{
    public function __construct(?string $filterName, ConstraintViolationListInterface $errors)
    {
        $message = \sprintf('The configuration of the filter "%s" is not valid. ', $filterName);

        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $message .= \PHP_EOL.$error->getPropertyPath().': '.$error->getMessage();
        }

        parent::__construct($message);
    }
}
