<?php

namespace LAG\AdminBundle\Event;

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
     *
     * @param string $resourceName
     * @param array  $configuration
     * @param string $adminName
     * @param string $entityClass
     */
    public function __construct(string $resourceName, array $configuration, string $adminName, string $entityClass)
    {
        $this->configuration = $configuration;
        $this->entityClass = $entityClass;
        $this->resourceName = $resourceName;
        $this->adminName = $adminName;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getAdminName(): string
    {
        return $this->adminName;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
