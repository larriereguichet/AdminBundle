<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutingLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private ResourceRegistryInterface $resourceRegistry,
        private UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct();
    }

    public function load($resource, string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new RuntimeException('Do not add the Admin routing loader "lag_admin" twice');
        }
        $routes = new RouteCollection();
        $resources = $this->resourceRegistry->all();

        foreach ($resources as $resource) {
            $this->loadResource($resource, $routes);
        }

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
            $routes->add($operation->getRoute(), new Route(
                $this->urlGenerator->generatePath($resource, $operation),
                [
                    '_controller' => $operation->getController(),
                    '_admin' => $operation->getResourceName(),
                    '_action' => $operation->getName(),
                ],
                [],
                $identifiers,
                null,
                [],
                $operation->getMethods(),
            ));
        }
    }
}
