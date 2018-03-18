<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\ConfigurationEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\ResourceCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActionFactory
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * ActionFactory constructor.
     *
     * @param ResourceCollection       $resourceCollection
     * @param EventDispatcherInterface $eventDispatcher
     * @param ConfigurationFactory     $configurationFactory
     */
    public function __construct(
        ResourceCollection $resourceCollection,
        EventDispatcherInterface $eventDispatcher,
        ConfigurationFactory $configurationFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationFactory = $configurationFactory;
    }

    /**
     * @param string             $actionName
     * @param string             $adminName
     *
     * @param AdminConfiguration $adminConfiguration
     *
     * @return ActionInterface
     *
     * @throws Exception
     */
    public function create($actionName, $adminName, AdminConfiguration $adminConfiguration): ActionInterface
    {
        $event = new ConfigurationEvent(
            $actionName,
            $adminConfiguration->getParameter('actions'),
            $adminName,
            $adminConfiguration->getParameter('entity')
        );
        $this->eventDispatcher->dispatch(AdminEvents::ACTION_CONFIGURATION, $event);

        if (!array_key_exists($actionName, $event->getConfiguration())) {
            throw new Exception(
                'The action "'.$actionName.'" was not found  in the configuration of the admin "'.$adminName.'"'
            );
        }
        $actionConfiguration = $event->getConfiguration()[$actionName];
        $configuration = $this
            ->configurationFactory
            ->createActionConfiguration($actionName, $actionConfiguration, $adminName, $adminConfiguration)
        ;
        $class = $configuration->getParameter('class');
        $action = new $class($actionName, $configuration);

        if (!$action instanceof ActionInterface) {
            throw new Exception('The action class "'.$class.'" should implements '.ActionInterface::class);
        }

        return $action;
    }
}