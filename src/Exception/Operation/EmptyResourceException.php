<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Operation;

use LAG\AdminBundle\Exception\Exception;

class EmptyResourceException extends Exception
{
    public function __construct(?string $operationName)
    {
        parent::__construct(sprintf('The operation "%s" should be linked to a resource', $operationName));
    }
}
