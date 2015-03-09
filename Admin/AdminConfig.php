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

    public $maxPerPage;

    public function hydrateFromConfiguration(array $adminConfiguration, ContainerInterface $container)
    {
        // defaults values
        if (!array_key_exists('manager', $adminConfiguration)) {
            $adminConfiguration['manager'] = [];
        }
        if (!array_key_exists('max_per_page', $adminConfiguration)) {
            // by default, we take the general value
            $adminConfiguration['max_per_page'] = $container->getParameter('bluebear.admin.application')['max_per_page'];
        }
        // general values
        $this->controllerName = $adminConfiguration['controller'];
        $this->entityName = $adminConfiguration['entity'];
        $this->formType = $adminConfiguration['form'];;
        $this->maxPerPage = $adminConfiguration['max_per_page'];
        $this->actions = $adminConfiguration['actions'];
        $this->managerConfiguration = $adminConfiguration['manager'];
    }
}