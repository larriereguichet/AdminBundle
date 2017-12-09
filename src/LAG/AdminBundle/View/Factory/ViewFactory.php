<?php

namespace LAG\AdminBundle\View\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Factory\ConfigurationFactory;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Field\Factory\FieldFactory;
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
     * ViewFactory constructor.
     *
     * @param ConfigurationFactory $configurationFactory
     * @param FieldFactory         $fieldFactory
     */
    public function __construct(ConfigurationFactory $configurationFactory, FieldFactory $fieldFactory)
    {
        $this->configurationFactory = $configurationFactory;
        $this->fieldFactory = $fieldFactory;
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
