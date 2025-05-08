<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

class Exception extends \Exception
{
    public function __construct(string $message, mixed ...$parameters)
    {
        parent::__construct(\sprintf($message, ...$parameters));
    }
}
