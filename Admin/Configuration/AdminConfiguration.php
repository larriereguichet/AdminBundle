<?php

namespace LAG\AdminBundle\Admin\Configuration;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Ease Admin configuration manipulation
 */
class AdminConfiguration
{
    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * Custom repository service id
     *
     * @var string
     */
    protected $repositoryServiceId;

    /**
     * @var string
     */
    protected $formType;

    /**
     * @var array
     */
    protected $actions = [];

    /**
     * @var int
     */
    protected $maxPerPage = 25;

    /**
     * @var string
     */
    protected $routingNamePattern;

    /**
     * @var string
     */
    protected $routingUrlPattern;

    /**
     * @var array
     */
    protected $adminConfiguration;

    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * AdminConfiguration constructor.
     *
     * @param array $adminConfiguration
     * @param ClassMetadata|null $metadata
     */
    public function __construct(array $adminConfiguration, ClassMetadata $metadata = null)
    {
        // defines values
        $this->controllerName = $adminConfiguration['controller'];
        $this->manager = $adminConfiguration['manager'];
        $this->entityName = $adminConfiguration['entity'];
        $this->formType = $adminConfiguration['form'];
        $this->actions = $adminConfiguration['actions'];
        $this->maxPerPage = $adminConfiguration['max_per_page'];
        $this->routingNamePattern = $adminConfiguration['routing_name_pattern'];
        $this->routingUrlPattern = $adminConfiguration['routing_url_pattern'];
        $this->adminConfiguration = $adminConfiguration;
        $this->metadata = $metadata;
        // user custom repository service id
        $this->repositoryServiceId = $adminConfiguration['repository'];
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return string
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return int
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * @return string
     */
    public function getRoutingNamePattern()
    {
        return $this->routingNamePattern;
    }

    /**
     * @param string $routingNamePattern
     */
    public function setRoutingNamePattern($routingNamePattern)
    {
        $this->routingNamePattern = $routingNamePattern;
    }

    /**
     * @return string
     */
    public function getRoutingUrlPattern()
    {
        return $this->routingUrlPattern;
    }

    /**
     * @param string $routingUrlPattern
     */
    public function setRoutingUrlPattern($routingUrlPattern)
    {
        $this->routingUrlPattern = $routingUrlPattern;
    }

    /**
     * @return ClassMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Return user custom repository service id. If none is configured, then return null
     *
     * @return string
     */
    public function getRepositoryServiceId()
    {
        return $this->repositoryServiceId;
    }
}
