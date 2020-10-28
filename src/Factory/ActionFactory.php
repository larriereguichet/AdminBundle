<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\Action;
use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\ActionEvent;
use LAG\AdminBundle\Factory\Configuration\ActionConfigurationFactoryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ActionFactory implements ActionFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private ActionConfigurationFactoryInterface $configurationFactory;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ActionConfigurationFactoryInterface $configurationFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationFactory = $configurationFactory;
    }

    public function create(string $actionName, array $options = []): ActionInterface
    {
        $configuration = $this->configurationFactory->create($actionName, $options);
        $action = new Action($actionName, $configuration);

        $event = new ActionEvent($action);
        $this->eventDispatcher->dispatch($event, AdminEvents::ACTION_CREATE);

        return $action;
    }
}
