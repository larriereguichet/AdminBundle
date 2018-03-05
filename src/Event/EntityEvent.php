<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use Symfony\Component\EventDispatcher\Event;

class EntityEvent extends Event
{
    private $entities;

    /**
     * @var AdminConfiguration
     */
    private $configuration;

    /**
     * @var ActionConfiguration
     */
    private $actionConfiguration;

    public function __construct(AdminConfiguration $configuration, ActionConfiguration $actionConfiguration)
    {
        $this->configuration = $configuration;
        $this->actionConfiguration = $actionConfiguration;
    }

    /**
     * @return mixed
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @param mixed $entities
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    /**
     * @return AdminConfiguration
     */
    public function getConfiguration(): AdminConfiguration
    {
        return $this->configuration;
    }

    /**
     * @return ActionConfiguration
     */
    public function getActionConfiguration(): ActionConfiguration
    {
        return $this->actionConfiguration;
    }
}
