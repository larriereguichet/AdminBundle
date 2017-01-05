<?php

namespace LAG\AdminBundle\Admin\Event;

use Symfony\Component\EventDispatcher\Event;

class BeforeConfigurationEvent extends Event
{
    /**
     * An array containing all the admins configuration, indexed by admin name.
     *
     * @var array
     */
    protected $adminConfigurations = [];

    /**
     * BeforeConfigurationEvent constructor.
     *
     * @param $adminsConfiguration
     */
    public function __construct($adminsConfiguration)
    {
        $this->adminConfigurations = $adminsConfiguration;
    }

    /**
     * @return array
     */
    public function getAdminConfigurations()
    {
        return $this->adminConfigurations;
    }
}
