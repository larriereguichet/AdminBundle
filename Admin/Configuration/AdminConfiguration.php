<?php

namespace BlueBear\AdminBundle\Admin\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

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

    public function __construct(array $adminConfiguration, ApplicationConfiguration $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'entity',
            'form'
        ]);
        $resolver->setDefaults([
            'controller' => 'BlueBearAdminBundle:Generic',
            'manager' => [],
            'form' => 'generic',
            'actions' => [],
            'max_per_page' => 25,
            'routing_url_pattern' => $configuration->getRoutingUrlPattern(),
            'routing_name_pattern' => $configuration->getRoutingNamePattern(),
        ]);
        // resolve configuration
        $adminConfiguration = $resolver->resolve($adminConfiguration);
        // defines values
        $this->controllerName = $adminConfiguration['controller'];
        $this->managerConfiguration = $adminConfiguration['manager'];
        $this->entityName = $adminConfiguration['entity'];
        $this->formType = $adminConfiguration['form'];;
        $this->actions = $adminConfiguration['actions'];
        $this->maxPerPage = $adminConfiguration['max_per_page'];
        $this->routingNamePattern = $adminConfiguration['routing_name_pattern'];
        $this->routingUrlPattern = $adminConfiguration['routing_url_pattern'];
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
