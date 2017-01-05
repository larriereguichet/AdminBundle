<?php

namespace LAG\AdminBundle\Action\Configuration;

use Exception;
use LAG\AdminBundle\Admin\AdminInterface;

class ConfigurationException extends Exception
{
    /**
     * ConfigurationException constructor.
     *
     * @param string $message
     * @param int $actionName
     * @param AdminInterface|null $admin
     */
    public function __construct($message, $actionName, AdminInterface $admin = null)
    {
        if (null !== $admin) {
            $adminName = $admin->getName();
        } else {
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
