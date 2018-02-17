<?php

namespace LAG\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Action\Event\ActionEvents;
use LAG\AdminBundle\Action\Event\BeforeConfigurationEvent;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationFactory
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * ConfigurationFactory constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

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
        // BeforeConfigurationEvent allows users to override action configuration before the resolving
        $event = new BeforeConfigurationEvent(
            $actionName,
            $configuration,
            $adminName,
            $adminConfiguration
        );
        $this
            ->eventDispatcher
            ->dispatch(ActionEvents::BEFORE_CONFIGURATION, $event)
        ;

        $resolver = new OptionsResolver();
        $actionConfiguration = new ActionConfiguration($actionName, $adminName, $adminConfiguration);
        $actionConfiguration->configureOptions($resolver);
    
        $parameters = $resolver->resolve($configuration);
        $actionConfiguration->setParameters($parameters);
        
        return $actionConfiguration;
    }
}
