<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\DataProvider;

class ClassNotSupportedException extends \Exception
{
    public function __construct(string $class)
    {
        parent::__construct(sprintf('The class "%s" is not supported by any data providers', $class));
    }
}
