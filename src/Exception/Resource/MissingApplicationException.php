<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Resource;

use LAG\AdminBundle\Exception\Exception;

class MissingApplicationException extends Exception
{
    public function __construct(string $resourceName)
    {
        parent::__construct('The application "%s" does not exist', $resourceName);
    }
}
