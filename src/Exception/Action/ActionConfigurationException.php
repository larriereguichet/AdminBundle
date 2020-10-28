<?php

namespace LAG\AdminBundle\Exception\Action;

use LAG\AdminBundle\Exception\Exception;
use Throwable;

class ActionConfigurationException extends Exception
{
    public function __construct(string $actionName, Throwable $previous = null)
    {
        $message = sprintf('An error occurred when configuring the action "%s".', $actionName);

        parent::__construct($message, $previous->getCode(), $previous);
    }
}
