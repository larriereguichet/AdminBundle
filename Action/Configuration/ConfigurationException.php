<?php

namespace LAG\AdminBundle\Action\Configuration;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;

class ConfigurationException extends Exception
{
    public function __construct($message, $actionName, AdminInterface $admin)
    {
        $message .= sprintf(
            ', for Admin %s and action %s',
            $admin->getName(),
            $actionName
        );

        parent::__construct($message);
    }
}
