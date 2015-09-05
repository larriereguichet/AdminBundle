<?php

namespace BlueBear\AdminBundle\Admin\Configuration;

class AdminConfiguration
{
    protected $controllerName;

    protected $entityName;

    protected $managerConfiguration;

    protected $formType;

    protected $actions;

    protected $maxPerPage = 25;

    protected $routingNamePattern;

    protected $routingUrlPattern;

    public function hydrateFromConfiguration(array $adminConfiguration, $applicationName)
    {
        $applicationConfiguration = $applicationName;
        // defaults values
        if (!array_key_exists('manager', $adminConfiguration)) {
            $adminConfiguration['manager'] = [];
        }
        if (!array_key_exists('max_per_page', $adminConfiguration)) {
            if (!array_key_exists('max_per_page', $applicationConfiguration)) {
                $applicationConfiguration['max_per_page'] = 25;
            }
            // by default, we take the general value
            $adminConfiguration['max_per_page'] = $applicationConfiguration['max_per_page'];
        }
        if (array_key_exists('routing', $applicationConfiguration)) {
            $adminConfiguration['routing'] = $applicationConfiguration['routing'];
        }
        // general values
        $this->controllerName = $adminConfiguration['controller'];
        $this->entityName = $adminConfiguration['entity'];
        $this->formType = $adminConfiguration['form'];;
        $this->maxPerPage = $adminConfiguration['max_per_page'];
        $this->actions = $adminConfiguration['actions'];
        $this->managerConfiguration = $adminConfiguration['manager'];
        $this->routingNamePattern = $adminConfiguration['routing']['name_pattern'];
        $this->routingUrlPattern = $adminConfiguration['routing']['url_pattern'];
    }

    /**
     * @return mixed
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param mixed $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param mixed $entityName
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
    }

    /**
     * @return mixed
     */
    public function getManagerConfiguration()
    {
        return $this->managerConfiguration;
    }

    /**
     * @param mixed $managerConfiguration
     */
    public function setManagerConfiguration($managerConfiguration)
    {
        $this->managerConfiguration = $managerConfiguration;
    }

    /**
     * @return mixed
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @param mixed $formType
     */
    public function setFormType($formType)
    {
        $this->formType = $formType;
    }

    /**
     * @return mixed
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param mixed $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * @return int
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * @param int $maxPerPage
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = $maxPerPage;
    }

    /**
     * @return mixed
     */
    public function getRoutingNamePattern()
    {
        return $this->routingNamePattern;
    }

    /**
     * @param mixed $routingNamePattern
     */
    public function setRoutingNamePattern($routingNamePattern)
    {
        $this->routingNamePattern = $routingNamePattern;
    }

    /**
     * @return mixed
     */
    public function getRoutingUrlPattern()
    {
        return $this->routingUrlPattern;
    }

    /**
     * @param mixed $routingUrlPattern
     */
    public function setRoutingUrlPattern($routingUrlPattern)
    {
        $this->routingUrlPattern = $routingUrlPattern;
    }
}
