<?php

namespace LAG\AdminBundle\Action\Configuration;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;

class ConfigurationException extends Exception
{
    /**
     * ConfigurationException constructor.
     *
     * @param string      $message
     * @param int         $actionName
     * @param string|null $adminName
     */
    public function __construct($message, $actionName, $adminName = null)
    {
        if (!$adminName) {
            $adminName = 'unknown';
        }
        $message .= sprintf(
            ', for Admin %s and action %s',
            $adminName,
            $actionName
        );
        
        parent::__construct($message);
    }
}
