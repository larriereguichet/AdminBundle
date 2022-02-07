<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Admin\Resource\Registry\ResourceRegistryInterface;
use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class RoutingLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private ResourceRegistryInterface $resourceRegistry,
        private ResourceLoaderInterface $resourceLoader,
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
            $this->resourceLoader->loadRoutes($resource, $routes);
        }

        return $routes;
    }

    public function supports($resource, string $type = null): bool
    {
        return 'extra' === $type;
    }
}
