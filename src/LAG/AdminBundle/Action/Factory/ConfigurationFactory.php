<?php

namespace LAG\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationFactory
{
    /**
     * Create an action configuration object.
     *
     * @param string             $actionName
     * @param string             $adminName
     * @param AdminConfiguration $adminConfiguration
     * @param array              $configuration
     *
     * @return ActionConfiguration
     */
    public function create($actionName, $adminName, AdminConfiguration $adminConfiguration, array $configuration = [])
    {
        $resolver = new OptionsResolver();
        $actionConfiguration = new ActionConfiguration($actionName, $adminName, $adminConfiguration);
        $actionConfiguration->configureOptions($resolver);
    
        $parameters = $resolver->resolve($configuration);
        $actionConfiguration->setParameters($parameters);
        
        return $actionConfiguration;
    }
}
