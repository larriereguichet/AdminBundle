<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

class UnexpectedTypeException extends Exception
{
    public function __construct(mixed $value, string $expectedType)
    {
        parent::__construct(\sprintf('Expected argument of type "%s", "%s" given', $expectedType, get_debug_type($value)));
    }
}
