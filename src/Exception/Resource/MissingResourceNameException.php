<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Resource;

use LAG\AdminBundle\Exception\Exception;

class MissingResourceNameException extends Exception
{
    public function __construct(?string $resourceClass = null)
    {
        parent::__construct('The resource "%s" has no name', $resourceClass ?? '');
    }
}
