<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Loader;

use LAG\AdminBundle\Metadata\ResourceInterface;
use LAG\AdminBundle\Request\ContextBuilder\PartialContextBuilder;
use LAG\AdminBundle\Resource\Factory\ResourceCollectionFactoryInterface;
use LAG\AdminBundle\Routing\UrlGenerator\PathGeneratorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use function Symfony\Component\String\u;

final class ResourceRoutingLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(
        private readonly string $applicationParameter,
        private readonly string $resourceParameter,
        private readonly string $operationParameter,
        private readonly ResourceCollectionFactoryInterface $resourceCollectionFactory,
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
            $path = $this->pathGenerator->generatePath($operation);
            $defaults = [
                '_controller' => $operation->getController(),
                $this->applicationParameter => $operation->getResource()->getApplicationName(),
                $this->resourceParameter => $operation->getResource()->getShortName(),
                $this->operationParameter => $operation->getShortName(),
            ];

            $route = new Route($path, $defaults, [], $identifiers, null, [], $operation->getMethods());
            $routes->add($operation->getRoute(), $route);

            if ($operation->canBeEmbedded()) {
                $defaults[PartialContextBuilder::EMBEDDED_REQUEST_ATTRIBUTE] = true;
                $embeddedRouteName = (string) u($operation->getRoute())->append('_embedded');

                $routes->add($embeddedRouteName, new Route(
                    path: $this->pathGenerator->generateEmbeddedPath($operation),
                    defaults: $defaults,
                    options: $identifiers,
                    methods: $operation->getMethods(),
                ));
            }
        }
    }
}
