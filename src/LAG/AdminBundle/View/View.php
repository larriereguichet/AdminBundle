<?php

namespace LAG\AdminBundle\View;

use Doctrine\Common\Collections\ArrayCollection;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Field\FieldInterface;
use Pagerfanta\Pagerfanta;

class View implements ViewInterface
{
    /**
     * @var string
     */
    private $actionName;
    
    /**
     * @var ActionConfiguration
     */
    private $configuration;
    
    /**
     * @var string
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
    
    /**
     * @var bool
     */
    private $haveToPaginate = false;
    
    /**
     * @var int
     */
    private $totalCount = 0;
    
    /**
     * @var Pagerfanta
     */
    private $pager;
    
    /**
     * View constructor.
     *
     * @param                     $actionName
     * @param                     $adminName
     * @param ActionConfiguration $configuration
     * @param AdminConfiguration  $adminConfiguration
     * @param array               $fields
     */
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
    
    /**
     * @return ActionConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
    
    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }
    
    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }
    
    /**
     * @param $entities
     */
    public function setEntities($entities)
    {
        if ($entities instanceof Pagerfanta) {
            $this->pager = $entities;
            $this->entities = new ArrayCollection($entities->getCurrentPageResults());
            $this->haveToPaginate = true;
            $this->totalCount = $entities->count();
        } else {
            $this->entities = $entities;
            $this->totalCount = count($entities);
        }
    }
    
    /**
     * @return string
     */
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
    
    /**
     * @return bool
     */
    public function haveToPaginate()
    {
        return $this->haveToPaginate;
    }
    
    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }
    
    /**
     * @return Pagerfanta|null
     */
    public function getPager()
    {
        return $this->pager;
    }
}
