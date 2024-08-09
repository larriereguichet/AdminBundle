<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidaDataException extends ValidationException
{
    public function __construct(ConstraintViolationListInterface $errors)
    {
        $message = 'The following errors have been encountered when validating the resource data: ';

        parent::__construct($message, $errors);
    }
}
