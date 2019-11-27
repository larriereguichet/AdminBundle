<?php

namespace LAG\AdminBundle\Event\Events;

use Symfony\Component\EventDispatcher\Event;

class ConfigurationEvent extends Event
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var string
     */
    private $adminName;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $resourceName;

    /**
     * ConfigurationEvent constructor.
     */
    public function __construct(string $resourceName, array $configuration, string $adminName, string $entityClass)
    {
        $this->configuration = $configuration;
        $this->entityClass = $entityClass;
        $this->resourceName = $resourceName;
        $this->adminName = $adminName;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getAdminName(): string
    {
        return $this->adminName;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
