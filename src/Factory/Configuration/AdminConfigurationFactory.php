<?php

namespace LAG\AdminBundle\Factory\Configuration;

use Exception;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;
use LAG\AdminBundle\Exception\ConfigurationException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminConfigurationFactory implements AdminConfigurationFactoryInterface
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(string $adminName, array $options = []): AdminConfiguration
    {
        $event = new AdminConfigurationEvent($adminName, $options);
        $this->eventDispatcher->dispatch($event, AdminEvents::ADMIN_CONFIGURATION);

        try {
            $configuration = new AdminConfiguration();
            $configuration->configure($event->getConfiguration());
        } catch (Exception $exception) {
            throw new ConfigurationException('admin', $adminName, $exception);
        }

        return $configuration;
    }
}
