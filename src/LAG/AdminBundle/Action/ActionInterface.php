<?php

namespace LAG\AdminBundle\Action;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\AdminAwareInterface;

interface ActionInterface extends AdminAwareInterface
{
    /**
     * Define the Action configuration.
     *
     * @param ActionConfiguration $actionConfiguration
     */
    public function setConfiguration(ActionConfiguration $actionConfiguration);
    
    /**
     * Return the Action's configuration.
     *
     * @return ActionConfiguration
     */
    public function getConfiguration();
    
    /**
     * Return true if the Action require entity loading.
     *
     * @return bool
     */
    public function isLoadingRequired();
    
    /**
     * The Action's name.
     *
     * @return string
     */
    public function getName();
}
