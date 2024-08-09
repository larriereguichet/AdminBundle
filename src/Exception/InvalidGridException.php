<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidGridException extends ValidationException
{
    public function __construct(?string $gridName, ConstraintViolationListInterface $errors)
    {
        $message = \sprintf(
            'The configuration of the grid "%s" is not valid. The following errors have been encountered :',
            $gridName
        );

        parent::__construct($message, $errors);
    }
}
