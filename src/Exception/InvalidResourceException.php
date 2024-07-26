<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidResourceException extends ValidationException
{
    public function __construct(?string $resourceName, ConstraintViolationListInterface $errors)
    {
        $message = sprintf(
            'The configuration of the resource "%s" is not valid. The following errors have been encountered :',
            $resourceName
        );

        parent::__construct($message, $errors);
    }
}
