<?php

namespace BlueBear\AdminBundle\Admin;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminConfig
{
    public $controllerName;

    public $entityName;

    public $managerConfiguration;

    public $formType;

    public $actions;

    public $maxPerPage = 25;

    public $routingNamePattern;

    public $routingUrlPattern;

    public function hydrateFromConfiguration(array $adminConfiguration, ContainerInterface $container)
    {
        $applicationConfiguration = $container->getParameter('bluebear.admin.application');
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
}
