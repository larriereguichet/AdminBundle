<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\PathGeneratorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class ResourceRoutingLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private readonly string $applicationParameter,
        private readonly string $resourceParameter,
        private readonly string $operationParameter,
        private readonly DefinitionFactoryInterface $definitionFactory,
        private readonly ResourceFactoryInterface $resourceFactory,
        private readonly PathGeneratorInterface $pathGenerator,
    ) {
        parent::__construct();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add the Admin routing loader "lag_admin" twice');
        }
        $routes = new RouteCollection();
        $resourceNames = $this->definitionFactory->getResourceNames();

        foreach ($resourceNames as $resourceName) {
            $resource = $this->resourceFactory->create($resourceName);
            $this->loadResource($resource, $routes);
        }
        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, ?string $type = null): bool
    {
        return 'lag_admin' === $type;
    }

    private function loadResource(Resource $resource, RouteCollection $routes): void
    {
        $identifiers = [];

        foreach ($resource->getIdentifiers() as $identifier) {
            $identifiers[$identifier] = null;
        }

        foreach ($resource->getOperations() as $operation) {
            $path = $this->pathGenerator->generatePath($operation);
            $defaults = [
                '_controller' => $operation->getController(),
                $this->applicationParameter => $operation->getResource()->getApplication(),
                $this->resourceParameter => $operation->getResource()->getName(),
                $this->operationParameter => $operation->getShortName(),
            ];

            $route = new Route($path, $defaults, [], $identifiers, null, [], $operation->getMethods());
            $routes->add($operation->getRoute(), $route);
        }
    }
}
