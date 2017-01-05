<?php

namespace LAG\AdminBundle\View;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Field\FieldInterface;

class View implements ViewInterface
{
    /**
     * @var
     */
    private $actionName;
    
    /**
     * @var ActionConfiguration
     */
    private $configuration;
    /**
     * @var
     */
    private $adminName;
    
    /**
     * @var array
     */
    private $entities = [];
    
    /**
     * @var FieldInterface[]
     */
    private $fields;
    
    /**
     * @var AdminConfiguration
     */
    private $adminConfiguration;
    
    public function __construct(
        $actionName,
        $adminName,
        ActionConfiguration $configuration,
        AdminConfiguration $adminConfiguration,
        array $fields = []
    ) {
        $this->actionName = $actionName;
        $this->configuration = $configuration;
        $this->adminName = $adminName;
        $this->fields = $fields;
        $this->adminConfiguration = $adminConfiguration;
    }
    
    public function getConfiguration()
    {
        return $this->configuration;
    }
    
    public function getActionName()
    {
        return $this->actionName;
    }
    
    public function getEntities()
    {
        return $this->entities;
    }
    
    public function setEntities($entities)
    {
        $this->entities = $entities;
    }
    
    public function getName()
    {
        return $this->adminName;
    }
    
    /**
     * @return FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * @return AdminConfiguration
     */
    public function getAdminConfiguration()
    {
        return $this->adminConfiguration;
    }
}
