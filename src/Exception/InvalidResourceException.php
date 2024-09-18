<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidResourceException extends ValidationException
{
    public function __construct(
        ?string $resourceName,
        ?string $applicationName,
        ConstraintViolationListInterface $errors
    ) {
        $message = \sprintf(
            'The configuration of the resource "%s" of the application "%s" is not valid. The following errors have been encountered :',
            $resourceName ?? '',
            $applicationName ?? '',
        );

        parent::__construct($message, $errors);
    }
}
