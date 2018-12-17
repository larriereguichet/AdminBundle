<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\ConfigurationEvent;
use LAG\AdminBundle\Resource\ResourceCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationFactory
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ResourceCollection
     */
    private $resourceCollection;

    /**
     * ConfigurationFactory constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param ResourceCollection       $resourceCollection
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ResourceCollection $resourceCollection)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->resourceCollection = $resourceCollection;
    }

    /**
     * @param string                   $adminName
     * @param array                    $configuration
     * @param ApplicationConfiguration $applicationConfiguration
     *
     * @return AdminConfiguration
     */
    public function createAdminConfiguration(
        string $adminName,
        array $configuration,
        ApplicationConfiguration $applicationConfiguration
    ): AdminConfiguration {
        $event = new ConfigurationEvent($adminName, $configuration, $adminName, $configuration['entity']);
        $this->eventDispatcher->dispatch(Events::ADMIN_CONFIGURATION, $event);

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
    ): ActionConfiguration {
        $event = new ConfigurationEvent(
            $actionName,
            $adminConfiguration->getParameter('actions'),
            $adminName,
            $adminConfiguration->getParameter('entity')
        );
        $this->eventDispatcher->dispatch(Events::ACTION_CONFIGURATION, $event);

        $resolver = new OptionsResolver();
        $actionConfiguration = new ActionConfiguration($actionName, $adminName, $adminConfiguration);
        $actionConfiguration->configureOptions($resolver);
        $actionConfiguration->setParameters($resolver->resolve($configuration));

        return $actionConfiguration;
    }

    /**
     * Build the resources menu items.
     *
     * @return array
     */
    public function createResourceMenuConfiguration()
    {
        $menuConfiguration = [];

        foreach ($this->resourceCollection->all() as $resource) {
            $resourceConfiguration = $resource->getConfiguration();

            // Add only entry for the "list" action
            if (
                !key_exists('actions', $resourceConfiguration) ||
                null === $resourceConfiguration['actions'] ||
                !array_key_exists('list', $resourceConfiguration['actions'])
            ) {
                continue;
            }
            $menuConfiguration['items'][] = [
                'text' => ucfirst($resource->getName()),
                'admin' => $resource->getName(),
                'action' => 'list',
            ];
        }

        return $menuConfiguration;
    }
}
