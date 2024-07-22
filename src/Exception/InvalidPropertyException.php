<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidPropertyException extends ValidationException
{
    public function __construct(?string $propertyName, ConstraintViolationListInterface $errors)
    {
        $message = sprintf(
            'The configuration of the property "%s" is not valid. The following errors have been encountered :',
            $propertyName
        );

        parent::__construct($message, $errors);
    }
}
