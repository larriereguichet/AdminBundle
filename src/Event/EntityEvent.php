<?php

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @var Request
     */
    private $request;

    /**
     * EntityEvent constructor.
     *
     * @param AdminConfiguration  $configuration
     * @param ActionConfiguration $actionConfiguration
     * @param Request             $request
     */
    public function __construct(
        AdminConfiguration $configuration,
        ActionConfiguration $actionConfiguration,
        Request $request
    ) {
        $this->configuration = $configuration;
        $this->actionConfiguration = $actionConfiguration;
        $this->request = $request;
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

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
