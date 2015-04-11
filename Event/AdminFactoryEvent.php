<?php

namespace BlueBear\AdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class AdminFactoryEvent extends Event
{
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
