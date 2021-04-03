<?php

namespace LAG\AdminBundle\Routing;

use LAG\AdminBundle\Admin\Configuration\ActionConfigurationMapper;
use LAG\AdminBundle\Admin\Resource\AdminResource;
use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
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
    private ApplicationConfiguration $applicationConfiguration;
    private ConfigurationFactoryInterface $configurationFactory;
    private ResourceRegistryInterface $resourceRegistry;

    public function __construct(
        ResourceRegistryInterface $resourceRegistry,
        ApplicationConfiguration $applicationConfiguration,
        ConfigurationFactoryInterface $configurationFactory
    ) {
        $this->applicationConfiguration = $applicationConfiguration;
        $this->configurationFactory = $configurationFactory;
        $this->resourceRegistry = $resourceRegistry;
    }

    public function load($resource, string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new RuntimeException('Do not add the Admin "extra" loader twice');
        }
        $routes = new RouteCollection();
        $resources = $this->resourceRegistry->all();

        foreach ($resources as $name => $resource) {
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
            $mapper = new ActionConfigurationMapper();
            $actionConfigurationArray = array_merge($mapper->map($actionName, $configuration), $actionOptions);

            try {
                $actionConfiguration = $this
                    ->configurationFactory
                    ->createActionConfiguration($actionName, $actionConfigurationArray);
            } catch (Exception $exception) {
                throw new ConfigurationException('admin', $resource->getName(), $exception);
            }
            $route = new Route($actionConfiguration->getPath(), [], array_keys($actionConfiguration->getRouteParameters()));
            $routes->add($actionConfiguration->get('route'), $route);
        }
    }
}
