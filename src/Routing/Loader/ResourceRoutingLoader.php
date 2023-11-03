<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\PathGeneratorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use function Symfony\Component\Translation\t;

class ResourceRoutingLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private string $applicationParameter,
        private string $resourceParameter,
        private string $operationParameter,
        private ResourceRegistryInterface $resourceRegistry,
        private PathGeneratorInterface $pathGenerator,
    ) {
        parent::__construct();
    }

    public function load(mixed $resource, string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add the Admin routing loader "lag_admin" twice');
        }
        $routes = new RouteCollection();
        $resources = $this->resourceRegistry->all();

        foreach ($resources as $resource) {
            $this->loadResource($resource, $routes);
        }
        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, string $type = null): bool
    {
        return 'lag_admin' === $type;
    }

    private function loadResource(AdminResource $resource, RouteCollection $routes): void
    {
        $identifiers = [];

        foreach ($resource->getIdentifiers() as $identifier) {
            $identifiers[$identifier] = null;
        }

        foreach ($resource->getOperations() as $operation) {
            $path = $this->pathGenerator->generatePath($operation);
            $defaults = [
                '_controller' => $operation->getController(),
                $this->applicationParameter => $operation->getResource()->getApplicationName(),
                $this->resourceParameter => $operation->getResource()->getName(),
                $this->operationParameter => $operation->getName(),
            ];

            $route = new Route($path, $defaults, [], $identifiers, null, [], $operation->getMethods());
            $routes->add($operation->getRoute(), $route);
        }
    }
}
