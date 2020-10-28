<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Event\AdminEvents;
use LAG\AdminBundle\Event\Events\AdminEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\Configuration\AdminConfigurationFactoryInterface;
use LAG\AdminBundle\LAGAdminBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminFactory implements AdminFactoryInterface
{
    private ResourceRegistryInterface $registry;
    private EventDispatcherInterface $eventDispatcher;
    private AdminConfigurationFactoryInterface $configurationFactory;
    private ApplicationConfiguration $applicationConfiguration;

    public function __construct(
        ResourceRegistryInterface $registry,
        EventDispatcherInterface $eventDispatcher,
        AdminConfigurationFactoryInterface $configurationFactory,
        ApplicationConfiguration $applicationConfiguration
    ) {
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationFactory = $configurationFactory;
        $this->applicationConfiguration = $applicationConfiguration;
    }

    public function createFromRequest(Request $request): AdminInterface
    {
        if (!$this->supports($request)) {
            throw new Exception('No admin resource was found in the request');
        }
        $resource = $this->registry->get($request->get('_route_params')[LAGAdminBundle::REQUEST_PARAMETER_ADMIN]);
        $configuration = $this->configurationFactory->create(
            $resource->getName(),
            $resource->getConfiguration()
        );

        $adminClass = $configuration->getAdminClass();
        $admin = new $adminClass($resource, $configuration, $this->eventDispatcher);

        $this->eventDispatcher->dispatch(new AdminEvent($admin), AdminEvents::ADMIN_CREATE);

        return $admin;
    }

    /**
     * Return true if the current Request is supported. Supported means that the Request has the required valid
     * parameters to get an admin from the registry.
     */
    public function supports(Request $request): bool
    {
        $routeParameters = $request->get('_route_params');

        if (!is_array($routeParameters)) {
            return false;
        }

        if (!key_exists(LAGAdminBundle::REQUEST_PARAMETER_ADMIN, $routeParameters) ||
            !key_exists(LAGAdminBundle::REQUEST_PARAMETER_ACTION, $routeParameters)
        ) {
            return false;
        }

        if (!$this->registry->has($routeParameters[LAGAdminBundle::REQUEST_PARAMETER_ADMIN])) {
            return false;
        }

        return true;
    }
}
