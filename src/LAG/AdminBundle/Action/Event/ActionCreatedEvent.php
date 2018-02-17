<?php

namespace LAG\AdminBundle\Action\Event;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Symfony\Component\EventDispatcher\Event;

class ActionCreatedEvent extends Event
{
    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var string
     */
    private $adminName;

    /**
     * @var AdminConfiguration
     */
    private $adminConfiguration;

    /**
     * ActionCreatedEvent constructor.
     *
     * @param ActionInterface    $action
     * @param string             $adminName
     * @param AdminConfiguration $adminConfiguration
     */
    public function __construct(ActionInterface $action, $adminName, AdminConfiguration $adminConfiguration)
    {
        $this->action = $action;
        $this->adminName = $adminName;
        $this->adminConfiguration = $adminConfiguration;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getAdminName()
    {
        return $this->adminName;
    }

    /**
     * @return AdminConfiguration
     */
    public function getAdminConfiguration()
    {
        return $this->adminConfiguration;
    }
}
