<?php

namespace LAG\AdminBundle\Admin\Event;

use Symfony\Component\EventDispatcher\Event;

class AdminFactoryEvent extends Event
{
    /**
     * Dispatched before Admins creation.
     */
    const EVENT_BEFORE_CONFIGURATION_LOAD = 'lag.admin.beforeConfigurationLoad';

    /**
     * Dispatched before the creation of an Admin.
     */
    const ADMIN_CREATION = 'lag.admin.adminCreation';

    /**
     * The configuration of all Admins, read from the yml configuration files
     *
     * @var array
     */
    protected $adminsConfiguration = [];

    /**
     * The configuration of the current Admin, empty array before the Admin configuration loading.
     *
     * @var array
     */
    protected $adminConfiguration = [];

    /**
     * The name of the current Admin. If no Admin configuration was loaded, equals to null
     *
     * @var string
     */
    protected $adminName = null;

    /**
     * @return array
     */
    public function getAdminsConfiguration()
    {
        return $this->adminsConfiguration;
    }

    /**
     * @param array $adminsConfiguration
     */
    public function setAdminsConfiguration(array $adminsConfiguration)
    {
        $this->adminsConfiguration = $adminsConfiguration;
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

    /**
     * @return string|null
     */
    public function getAdminName()
    {
        return $this->adminName;
    }

    /**
     * @param string $adminName
     */
    public function setAdminName($adminName)
    {
        $this->adminName = $adminName;
    }
}
