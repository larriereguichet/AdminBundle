<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\View\View;

class ViewFactory
{
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * ViewFactory constructor.
     *
     * @param ConfigurationFactory $configurationFactory
     * @param FieldFactory         $fieldFactory
     * @param MenuFactory          $menuFactory
     */
    public function __construct(
        ConfigurationFactory $configurationFactory,
        FieldFactory $fieldFactory,
        MenuFactory $menuFactory
    ) {
        $this->configurationFactory = $configurationFactory;
        $this->fieldFactory = $fieldFactory;
        $this->menuFactory = $menuFactory;
    }
    
    /**
     * Create a view for a given Admin and Action.
     *
     * @param string              $actionName
     * @param string              $adminName
     * @param AdminConfiguration  $adminConfiguration
     * @param ActionConfiguration $actionConfiguration
     *
     * @return View
     */
    public function create(
        $actionName,
        $adminName,
        AdminConfiguration $adminConfiguration,
        ActionConfiguration $actionConfiguration
    ) {
        $fields = $this
            ->fieldFactory
            ->getFields($actionConfiguration)
        ;
        $view = new View($actionName, $adminName, $actionConfiguration, $adminConfiguration, $fields);
    
        return $view;
    }
}
