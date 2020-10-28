<?php

namespace LAG\AdminBundle\Routing;

use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Controller\HomeAction;
use LAG\AdminBundle\Factory\Configuration\ConfigurationFactoryInterface;
use RuntimeException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutingLoader implements LoaderInterface
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
            $configuration = $this
                ->configurationFactory
                ->createAdminConfiguration($resource->getName(), $resource->getConfiguration())
            ;

            foreach ($configuration->get('actions') as $actionName => $actionOptions) {
                $actionConfiguration = $this
                    ->configurationFactory
                    ->createActionConfiguration($actionName, $actionOptions)
                ;
                $route = new Route($actionConfiguration->getPath(), [], array_keys($actionConfiguration->getRouteParameters()));
                $routes->add($actionConfiguration->get('route'), $route);
            }

            if ($this->applicationConfiguration->get('enable_homepage')) {
                $route = new Route('/', ['_controller' => HomeAction::class], []);
                $routes->add('lag_admin.homepage', $route);
            }
        }

        return $routes;
    }

    public function supports($resource, string $type = null): bool
    {
        return 'extra' === $type;
    }

    public function getResolver()
    {
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
