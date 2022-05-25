<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Action\Factory;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\ActionEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ActionFactory implements ActionFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ActionConfigurationFactoryInterface $configurationFactory,
        private ResourceRegistryInterface $registry
    ) {
    }

    public function create(string $actionName, array $options = []): ActionInterface
    {
        if (empty($options['admin_name'])) {
            throw new Exception(sprintf('The "admin_name" key is missing in the options of action "%s"', $actionName));
        }
        $resource = $this->registry->get($options['admin_name']);
        $resourceConfiguration = $resource->getConfiguration();

        if (!empty($resourceConfiguration['actions'][$actionName])) {
            $options = array_merge($resourceConfiguration['actions'][$actionName], $options);
        }
        $configuration = $this->configurationFactory->create($options['admin_name'], $actionName, $options);
        $actionClass = $configuration->getActionClass();
        $action = new $actionClass($actionName, $configuration);

        $event = new ActionEvent($action);
        $this->eventDispatcher->dispatch($event, AdminEvents::ACTION_CREATE);

        return $action;
    }
}
