<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends Exception
{
    public function __construct(?string $message = '', ConstraintViolationListInterface $errors)
    {
        /** @var ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $message .= \PHP_EOL.'"'.$error->getPropertyPath().'"'.': '.$error->getMessage().PHP_EOL;
        }

        parent::__construct($message);
    }
}
