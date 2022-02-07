<?php

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Exception\ConfigurationException;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ResourceLoader implements ResourceLoaderInterface
{
    public function __construct(private ConfigurationFactoryInterface $configurationFactory)
    {
    }

    public function loadRoutes(AdminResource $resource, RouteCollection $routes): void
    {
        $configuration = $this
            ->configurationFactory
            ->createAdminConfiguration($resource->getName(), $resource->getConfiguration())
        ;

        foreach ($configuration->get('actions') as $name => $options) {
            try {
                $options['admin_name'] = $resource->getName();
                $actionConfiguration = $this
                    ->configurationFactory
                    ->createActionConfiguration($name, $options);
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
