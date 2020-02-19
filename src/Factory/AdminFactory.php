<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Configuration\ApplicationConfigurationStorage;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminFactory
{
    /**
     * @var ResourceRegistryInterface
     */
    private $registry;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * @var ApplicationConfiguration
     */
    private $applicationConfiguration;

    /**
     * AdminFactory constructor.
     */
    public function __construct(
        ResourceRegistryInterface $registry,
        EventDispatcherInterface $eventDispatcher,
        ConfigurationFactory $configurationFactory,
        ApplicationConfigurationStorage $applicationConfigurationStorage
    ) {
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;
        $this->configurationFactory = $configurationFactory;
        $this->applicationConfiguration = $applicationConfigurationStorage->getConfiguration();
    }

    /**
     * @return AdminInterface
     *
     * @throws Exception
     */
    public function createFromRequest(Request $request)
    {
        if (!$this->supports($request)) {
            throw new Exception('No admin resource was found in the request');
        }
        $resource = $this->registry->get($request->get(LAGAdminBundle::REQUEST_PARAMETER_ADMIN));
        $configuration = $this
            ->configurationFactory
            ->createAdminConfiguration(
                $resource->getName(),
                $resource->getConfiguration(),
                $this->applicationConfiguration
            )
        ;
        $adminClass = $configuration->getParameter('class');
        $admin = new $adminClass($resource, $configuration, $this->eventDispatcher);

        if (!$admin instanceof AdminInterface) {
            throw new Exception('The admin class "'.$adminClass.'" should implements '.AdminInterface::class);
        }

        return $admin;
    }

    /**
     * Return true if the current Request is supported. Supported means that the Request has the required valid
     * parameters to get an admin from the registry.
     *
     * @return bool
     */
    public function supports(Request $request)
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
