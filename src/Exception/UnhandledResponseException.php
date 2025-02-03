<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception;

final class UnhandledResponseException extends Exception
{
    public function __construct()
    {
        parent::__construct('No matching response handler found');
    }
}