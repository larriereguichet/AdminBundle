<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Admin\AdminInterface;
use Symfony\Component\EventDispatcher\Event;

class AdminEvent extends Event
{
    const ADMIN_CREATE = 'event.admin.create';
    const ACTION_CREATE = 'event.action.create';

    protected $configuration;

    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @param array $configuration
     * @return AdminEvent
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param AdminInterface $admin
     * @return $this
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }
}
