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

    public function hydrateFromConfiguration(array $adminConfiguration, ContainerInterface $container)
    {
        // defaults values
        if (!array_key_exists('manager', $adminConfiguration)) {
            $adminConfiguration['manager'] = [];
        }
        if (!array_key_exists('max_per_page', $adminConfiguration)) {
            // by default, we take the general value
            $generalConfiguration = $container->getParameter('bluebear.admin.application');

            if (!array_key_exists('max_per_page', $generalConfiguration)) {
                $generalConfiguration['max_per_page'] = 25;
            }
            $adminConfiguration['max_per_page'] = $generalConfiguration['max_per_page'];
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