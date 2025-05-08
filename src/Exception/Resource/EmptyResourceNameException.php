<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Resource;

use LAG\AdminBundle\Exception\Exception;

class EmptyResourceNameException extends Exception
{
    public function __construct(string $resourceClass)
    {
        return parent::__construct('The resource "%s" has no name', $resourceClass);
    }
}
