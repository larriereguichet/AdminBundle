<?php

namespace LAG\AdminBundle\View\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Factory\ConfigurationFactory;
use LAG\AdminBundle\Admin\AdminInterface;
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
    
    public function __construct(ConfigurationFactory $configurationFactory, FieldFactory $fieldFactory)
    {
        $this->configurationFactory = $configurationFactory;
        $this->fieldFactory = $fieldFactory;
    }
    
    public function create($actionName, $adminName, AdminConfiguration $adminConfiguration, array $actionConfiguration)
    {
        $configuration = $this
            ->configurationFactory
            ->create($actionName, $adminName, $adminConfiguration, $actionConfiguration)
        ;
    
        $fields = $this
            ->fieldFactory
            ->getFields($configuration)
        ;
        $view = new View($actionName, $adminName, $configuration, $adminConfiguration, $fields);
    
        return $view;
    }
}
