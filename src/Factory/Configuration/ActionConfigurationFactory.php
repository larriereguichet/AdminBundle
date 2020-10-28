<?php

namespace LAG\AdminBundle\Factory\Configuration;

use Exception;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\Configuration\ActionConfigurationEvent;
use LAG\AdminBundle\Exception\Action\ActionConfigurationException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ActionConfigurationFactory implements ActionConfigurationFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(string $actionName, array $options = []): ActionConfiguration
    {
        $options['name'] = $actionName;
        $event = new ActionConfigurationEvent($actionName, $options);
        $this->eventDispatcher->dispatch($event, AdminEvents::ACTION_CONFIGURATION);
        $configuration = new ActionConfiguration();

        try {
            $configuration->configure($event->getConfiguration());
        } catch (Exception $exception) {
            throw new ActionConfigurationException($actionName, $exception);
        }

        return $configuration;
    }
}
