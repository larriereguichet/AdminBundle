<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminFactory implements AdminFactoryInterface
{
    public function __construct(
        private ResourceRegistryInterface $registry,
        private EventDispatcherInterface $eventDispatcher,
        private AdminConfigurationFactoryInterface $configurationFactory
    ) {
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
