<?php

namespace LAG\AdminBundle\Factory;

use Exception;
use LAG\AdminBundle\Admin\Helper\AdminHelperInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\MenuConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\ConfigurationEvent;
use LAG\AdminBundle\Event\Menu\MenuConfigurationEvent;
use LAG\AdminBundle\Exception\ConfigurationException;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\Resolver\RoutingResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

// TODO split in several class in several namespaces
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
     * @var RoutingResolverInterface
     */
    private $routingResolver;

    /**
     * @var AdminHelperInterface
     */
    private $adminHelper;

    /**
     * ConfigurationFactory constructor.
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ResourceRegistryInterface $registry,
        RoutingResolverInterface $resolver,
        AdminHelperInterface $adminHelper
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->registry = $registry;
        $this->routingResolver = $resolver;
        $this->adminHelper = $adminHelper;
    }

    public function createAdminConfiguration(
        string $adminName,
        array $configuration,
        ApplicationConfiguration $applicationConfiguration
    ): AdminConfiguration {
        $event = new ConfigurationEvent($adminName, $configuration, $adminName, $configuration['entity']);
        $this->eventDispatcher->dispatch($event, Events::CONFIGURATION_ADMIN);

        try {
            $resolver = new OptionsResolver();
            $adminConfiguration = new AdminConfiguration($adminName, $applicationConfiguration);
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
        $this->eventDispatcher->dispatch($event, Events::CONFIGURATION_ACTION);

        $resolver = new OptionsResolver();
        $actionConfiguration = new ActionConfiguration($actionName, $adminName, $adminConfiguration);
        $actionConfiguration->configureOptions($resolver);
        $actionConfiguration->setParameters($resolver->resolve($configuration));

        return $actionConfiguration;
    }

    public function createMenuConfiguration(string $menuName, array $configuration): MenuConfiguration
    {
        $event = new MenuConfigurationEvent($menuName, $configuration);
        $this->eventDispatcher->dispatch($event, Events::PRE_MENU_CONFIGURATION);
        $configuration = $event->getMenuConfiguration();
        $adminName = null;

        if (null !== $this->adminHelper->getCurrent()) {
            $adminName = $this->adminHelper->getCurrent()->getName();
            $actionMenus = $this->adminHelper->getCurrent()->getAction()->getConfiguration()->get('menus');
            $inherits = empty($configuration['inherits']) || false === $configuration['inherits'];

            if (!empty($actionMenus[$menuName])) {
                if ($inherits) {
                    $event->setMenuConfiguration(array_merge_recursive($configuration, $actionMenus[$menuName]));
                } else {
                    $event->setMenuConfiguration($actionMenus[$menuName]);
                }
            }
        }
        $this->eventDispatcher->dispatch($event, Events::MENU_CONFIGURATION);
        $configuration = $event->getMenuConfiguration();

        $resolver = new OptionsResolver();
        $menuConfiguration = new MenuConfiguration(
            $menuName,
            $adminName,
            $this->routingResolver,
            $this->adminHelper->getCurrent()->getEntities()->first()
        );
        $menuConfiguration->configureOptions($resolver);

        try {
            $menuConfiguration->setParameters($resolver->resolve($configuration));
        } catch (Exception $exception) {
            throw new ConfigurationException('menu', $menuName, $exception->getCode(), $exception);
        }

        return $menuConfiguration;
    }
}
