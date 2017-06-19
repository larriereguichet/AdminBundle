<?php

namespace LAG\AdminBundle\View;

use Doctrine\Common\Collections\Collection;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Pagerfanta\Pagerfanta;

interface ViewInterface
{
    /**
     * @return ActionConfiguration
     */
    public function getConfiguration();
    
    public function getName();
    
    public function getActionName();
    
    /**
     * @return Collection|Pagerfanta|array
     */
    public function getEntities();
    
    public function setEntities($entities);
    
    /**
     * @return AdminConfiguration
     */
    public function getAdminConfiguration();
}
