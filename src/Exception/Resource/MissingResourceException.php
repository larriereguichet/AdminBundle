<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Resource;

use LAG\AdminBundle\Exception\Exception;

class MissingResourceException extends Exception
{
    public function __construct(string $resourceName)
    {
        parent::__construct('The resource "%s" does not exist', $resourceName);
    }
}
