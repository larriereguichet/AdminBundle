<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Metadata\ResourceInterface;
use LAG\AdminBundle\Resource\Factory\ResourceCollectionFactoryInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use function Symfony\Component\String\u;

final class ResourceRoutingLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private readonly string $requestParameter,
        private readonly ResourceCollectionFactoryInterface $resourceCollectionFactory,
    ) {
        parent::__construct();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add the Admin routing loader "lag_admin" twice');
        }
        $routes = new RouteCollection();
        $resources = $this->resourceCollectionFactory->create();

        foreach ($resources as $adminResource) {
            $this->loadResource($adminResource, $routes);
        }
        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, ?string $type = null): bool
    {
        return 'lag_admin' === $type;
    }

    private function loadResource(ResourceInterface $resource, RouteCollection $routes): void
    {
        $identifiers = [];

        foreach ($resource->getIdentifiers() as $identifier) {
            $identifiers[$identifier] = null;
        }

        foreach ($resource->getOperations() as $operation) {
            $path = (string) u()
                ->append($operation->getPath())
                ->ensureStart('/')
                ->trimEnd('/')
            ;
            $defaults = [
                '_controller' => $operation->getController(),
                $this->requestParameter => $operation->getName(),
            ];

            $route = new Route($path, $defaults, [], $identifiers, null, [], $operation->getMethods());
            $routes->add($operation->getRoute(), $route);
        }
    }
}
