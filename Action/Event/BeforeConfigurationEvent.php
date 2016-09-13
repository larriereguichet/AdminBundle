<?php

namespace LAG\AdminBundle\Action\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

class BeforeConfigurationEvent extends Event
{
    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @var array
     */
    protected $actionConfiguration;

    public function __construct($actionName, $actionConfiguration, AdminInterface $admin)
    {
        $this->actionName = $actionName;
        $this->actionConfiguration = $actionConfiguration;
        $this->admin = $admin;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @return array
     */
    public function getActionConfiguration()
    {
        return $this->actionConfiguration;
    }
}
