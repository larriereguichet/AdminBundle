<?php

namespace LAG\AdminBundle\Admin\Configuration;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Ease Admin configuration manipulation
 */
class AdminConfiguration
{
    /**
     * Admin name
     *
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * Custom data provider name
     *
     * @var string
     */
    protected $dataProvider;

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
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * Original admin configuration.
     *
     * @var array
     */
    protected $adminConfiguration;

    /**
     * AdminConfiguration constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->controllerName = $configuration['controller'];
        $this->entityName = $configuration['entity'];
        $this->formType = $configuration['form'];
        $this->actions = $configuration['actions'];
        $this->maxPerPage = $configuration['max_per_page'];
        $this->routingNamePattern = $configuration['routing_name_pattern'];
        $this->routingUrlPattern = $configuration['routing_url_pattern'];
        $this->adminConfiguration = $configuration;
        $this->dataProvider = $configuration['data_provider'];
        $this->metadata = $configuration['metadata'];
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
     * Return custom data provider name.
     *
     * @return string
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * Return original admin configuration.
     *
     * @return array
     */
    public function getAdminConfiguration()
    {
        return $this->adminConfiguration;
    }
}
