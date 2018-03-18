<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\ConfigurationEvent;
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
     * @param string                   $adminName
     * @param array                    $configuration
     * @param ApplicationConfiguration $applicationConfiguration
     * @return AdminConfiguration
     */
    public function createAdminConfiguration(
        string $adminName,
        array $configuration,
        ApplicationConfiguration $applicationConfiguration
    ): AdminConfiguration
    {
        $event = new ConfigurationEvent($adminName, $configuration, $adminName, $configuration['entity']);
        $this->eventDispatcher->dispatch(AdminEvents::ADMIN_CONFIGURATION, $event);

        $resolver = new OptionsResolver();
        $adminConfiguration = new AdminConfiguration($applicationConfiguration);
        $adminConfiguration->configureOptions($resolver);
        $adminConfiguration->setParameters($resolver->resolve($event->getConfiguration()));

        return $adminConfiguration;
    }

    /**
     * @param string             $actionName
     * @param array              $configuration
     * @param string             $adminName
     * @param AdminConfiguration $adminConfiguration
     *
     * @return ActionConfiguration
     */
    public function createActionConfiguration(
        string $actionName,
        array $configuration,
        string $adminName,
        AdminConfiguration $adminConfiguration
    ): ActionConfiguration
    {
        $event = new ConfigurationEvent(
            $actionName,
            $adminConfiguration->getParameter('actions'),
            $adminName,
            $adminConfiguration->getParameter('entity')
        );
        $this->eventDispatcher->dispatch(AdminEvents::ACTION_CONFIGURATION, $event);

        $resolver = new OptionsResolver();
        $actionConfiguration = new ActionConfiguration($actionName, $adminName, $adminConfiguration);
        $actionConfiguration->configureOptions($resolver);
        $actionConfiguration->setParameters($resolver->resolve($configuration));

        return $actionConfiguration;
    }
}
