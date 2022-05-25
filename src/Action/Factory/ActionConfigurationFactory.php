<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Action\Factory;

use Exception;
use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\Configuration\ActionConfigurationEvent;
use LAG\AdminBundle\Exception\Action\ActionConfigurationException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ActionConfigurationFactory implements ActionConfigurationFactoryInterface
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function create(string $adminName, string $actionName, array $options = []): ActionConfiguration
    {
        $options['name'] = $actionName;
        $options['admin_name'] = $adminName;
        $event = new ActionConfigurationEvent($adminName, $actionName, $options);
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
