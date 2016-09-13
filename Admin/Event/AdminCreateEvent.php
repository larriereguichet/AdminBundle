<?php

namespace LAG\AdminBundle\Admin\Event;

use Symfony\Component\EventDispatcher\Event;

class AdminCreateEvent extends Event
{
    /**
     * Admin name
     *
     * @var string
     */
    private $adminName;

    /**
     * Admin configuration
     *
     * @var array
     */
    private $adminConfiguration;

    /**
     * AdminCreateEvent constructor.
     *
     * @param $adminName
     * @param $adminConfiguration
     */
    public function __construct($adminName, $adminConfiguration)
    {
        $this->adminName = $adminName;
        $this->adminConfiguration = $adminConfiguration;
    }

    /**
     * @return string
     */
    public function getAdminName()
    {
        return $this->adminName;
    }

    /**
     * @return array
     */
    public function getAdminConfiguration()
    {
        return $this->adminConfiguration;
    }

    /**
     * @param array $adminConfiguration
     */
    public function setAdminConfiguration($adminConfiguration)
    {
        $this->adminConfiguration = $adminConfiguration;
    }
}
