<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Factory;

use Exception;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\Configuration\AdminConfigurationEvent;
use LAG\AdminBundle\Exception\ConfigurationException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminConfigurationFactory implements AdminConfigurationFactoryInterface
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
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
