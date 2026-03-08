<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

final class MissingOperationResourceException extends Exception
{
    public function __construct(string $operationName)
    {
        parent::__construct('The operation "%s" is not owned by any resource', $operationName);
    }
}
