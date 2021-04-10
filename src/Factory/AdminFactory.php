<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Factory\Configuration\AdminConfigurationFactoryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminFactory implements AdminFactoryInterface
{
    private ResourceRegistryInterface $registry;
    private EventDispatcherInterface $eventDispatcher;
    private AdminConfigurationFactoryInterface $configurationFactory;

    public function __construct(
        ResourceRegistryInterface $registry,
        EventDispatcherInterface $eventDispatcher,
        AdminConfigurationFactoryInterface $configurationFactory
    ) {
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationFactory = $configurationFactory;
    }

    public function create(string $name, array $options = []): AdminInterface
    {
        $resource = $this->registry->get($name);
        $configuration = $this
            ->configurationFactory
            ->create($name, array_merge($resource->getConfiguration(), $options))
        ;
        $adminClass = $configuration->getAdminClass();
        $admin = new $adminClass($name, $configuration, $this->eventDispatcher);

        $this->eventDispatcher->dispatch(new AdminEvent($admin), AdminEvents::ADMIN_CREATE);

        return $admin;
    }
}
