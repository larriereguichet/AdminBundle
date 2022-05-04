<?php

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Action\Factory\ActionConfigurationFactoryInterface;
use LAG\AdminBundle\Admin\Factory\AdminConfigurationFactoryInterface;
use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Exception\ConfigurationException;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ResourceLoader implements ResourceLoaderInterface
{
    public function __construct(
        private AdminConfigurationFactoryInterface $adminConfigurationFactory,
        private ActionConfigurationFactoryInterface $actionConfigurationFactory,
    ) {
    }

    public function loadRoutes(AdminResource $resource, RouteCollection $routes): void
    {
        $configuration = $this
            ->adminConfigurationFactory
            ->create($resource->getName(), $resource->getConfiguration())
        ;

        foreach ($configuration->get('actions') as $name => $options) {
            try {
                $actionConfiguration = $this->actionConfigurationFactory->create(
                    $resource->getName(),
                    $name,
                    $options
                );
            } catch (Exception $exception) {
                throw new ConfigurationException('admin', $resource->getName(), $exception);
            }
            $route = new Route($actionConfiguration->getPath(), [
                '_controller' => $actionConfiguration->getController(),
                '_admin' => $actionConfiguration->getAdminName(),
                '_action' => $actionConfiguration->getName(),
            ], array_keys($actionConfiguration->getRouteParameters()));

            $routes->add($actionConfiguration->get('route'), $route);
        }
    }
}
