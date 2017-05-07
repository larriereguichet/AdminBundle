<?php

namespace LAG\AdminBundle\Admin\Exception;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;

class AdminException extends Exception
{
    /**
     * AdminException constructor. Append the admin and the action name to the exception message
     *
     * @param string $message
     * @param string $actionName
     * @param AdminInterface $admin
     */
    public function __construct($message, $actionName = '', AdminInterface $admin)
    {
        $message .= sprintf(
            ', for Admin %s and action %s',
            $admin->getName(),
            $actionName
        );

        parent::__construct($message);
    }
}
