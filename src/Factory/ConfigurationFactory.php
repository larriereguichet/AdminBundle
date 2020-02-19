<?php

namespace LAG\AdminBundle\Factory;

use Exception;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\MenuConfiguration;
use LAG\AdminBundle\Configuration\MenuItemConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\ConfigurationEvent;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ConfigurationFactory
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ResourceRegistryInterface
     */
    private $registry;

    /**
     * ConfigurationFactory constructor.
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ResourceRegistryInterface $registry)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->registry = $registry;
    }

    public function createAdminConfiguration(
        string $adminName,
        array $configuration,
        ApplicationConfiguration $applicationConfiguration
    ): AdminConfiguration {
        $event = new ConfigurationEvent($adminName, $configuration, $adminName, $configuration['entity']);
        $this->eventDispatcher->dispatch(Events::CONFIGURATION_ADMIN, $event);

        try {
            $resolver = new OptionsResolver();
            $adminConfiguration = new AdminConfiguration($applicationConfiguration);
            $adminConfiguration->configureOptions($resolver);
            $adminConfiguration->setParameters($resolver->resolve($event->getConfiguration()));
        } catch (Exception $exception) {
            $message = 'The configuration of the admin "'.$adminName.'" is invalid: '.$exception->getMessage();

            throw new Exception($message, 500, $exception);
        }

        return $adminConfiguration;
    }

    public function createActionConfiguration(
        string $actionName,
        array $configuration,
        string $adminName,
        AdminConfiguration $adminConfiguration
    ): ActionConfiguration {
        $event = new ConfigurationEvent(
            $actionName,
            $adminConfiguration->getParameter('actions'),
            $adminName,
            $adminConfiguration->getParameter('entity')
        );
        $this->eventDispatcher->dispatch(Events::CONFIGURATION_ACTION, $event);

        $resolver = new OptionsResolver();
        $actionConfiguration = new ActionConfiguration($actionName, $adminName, $adminConfiguration);
        $actionConfiguration->configureOptions($resolver);
        $actionConfiguration->setParameters($resolver->resolve($configuration));

        return $actionConfiguration;
    }

    public function createMenuConfiguration(string $menuName, array $configuration): MenuConfiguration
    {
        $event = new MenuConfigurationEvent($menuName, $configuration);
        $this->eventDispatcher->dispatch($event, Events::MENU_CONFIGURATION);
        $configuration = $event->getMenuConfiguration();

        foreach ($configuration['children'] as $itemName => $itemConfiguration) {
            if (null === $itemConfiguration) {
                $itemConfiguration = [];
            }
            $configuration['children'][$itemName] = $this->createMenuItemConfiguration($itemName, $itemConfiguration);
        }
        $resolver = new OptionsResolver();
        $menuConfiguration = new MenuConfiguration($menuName);
        $menuConfiguration->configureOptions($resolver);
        $menuConfiguration->setParameters($resolver->resolve($configuration));

        return $menuConfiguration;
    }

    public function createMenuItemConfiguration(string $itemName, array $configuration): MenuItemConfiguration
    {
        $resolver = new OptionsResolver();
        $itemConfiguration = new MenuItemConfiguration($itemName);
        $itemConfiguration->configureOptions($resolver);
        $itemConfiguration->setParameters($resolver->resolve($configuration));

        return $itemConfiguration;
    }
}
