<?php

namespace LAG\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AdminFactoryEvent extends Event
{
    const ADMIN_CREATION = 'lag.admin.adminCreation';

    protected $adminsConfiguration = [];

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
    public function setAdminsConfiguration($adminsConfiguration)
    {
        $this->adminsConfiguration = $adminsConfiguration;
    }
}