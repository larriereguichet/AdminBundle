<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Action;

use LAG\AdminBundle\Exception\Exception;
use Throwable;

class ActionConfigurationException extends Exception
{
    public function __construct(string $actionName, Throwable $previous = null)
    {
        $message = sprintf(
            'The configuration of the action "%s" is not valid: %s',
            $actionName,
            $previous ? $previous->getMessage() : ''
        );

        parent::__construct($message, $previous?->getCode() ?? 0, $previous);
    }
}
