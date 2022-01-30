<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing;

use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Exception\ConfigurationException;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutingLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private ResourceRegistryInterface $resourceRegistry,
        private ConfigurationFactoryInterface $configurationFactory
    ) {
        parent::__construct();
    }

    public function load($resource, string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new RuntimeException('Do not add the Admin routing loader "extra" twice');
        }
        $routes = new RouteCollection();
        $resources = $this->resourceRegistry->all();

        foreach ($resources as $resource) {
            $this->configureAdminRoutes($resource, $routes);
        }

        return $routes;
    }

    public function supports($resource, string $type = null): bool
    {
        return 'extra' === $type;
    }

    private function configureAdminRoutes(AdminResource $resource, RouteCollection $routes): void
    {
        $configuration = $this
            ->configurationFactory
            ->createAdminConfiguration($resource->getName(), $resource->getConfiguration())
        ;

        foreach ($configuration->get('actions') as $actionName => $actionOptions) {
            try {
                $actionOptions['admin_name'] = $resource->getName();
                $actionConfiguration = $this
                    ->configurationFactory
                    ->createActionConfiguration($actionName, $actionOptions);
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
