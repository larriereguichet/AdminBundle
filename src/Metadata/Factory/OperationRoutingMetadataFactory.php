<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationMetadataInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class OperationRoutingMetadataFactory implements OperationMetadataFactoryInterface
{
    public function __construct(
        private OperationMetadataFactoryInterface $metadataFactory,
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function createMetadata(OperationMetadataInterface $operation): OperationMetadataInterface
    {
        $operation = $this->metadataFactory->createMetadata($operation);
        $resource = $operation->getResource();

        if ($operation->getRoute() === null) {
            $route = $this->routeNameGenerator->generateRouteName($resource, $operation);
            $operation = $operation->withRoute($route);
        }

        if ($operation->getPath() === null) {
            $path = u();
            $inflector = new EnglishInflector();
            $prefix = $inflector->pluralize(u($resource->getShortName())->lower()->toString())[0];

            if ($resource->getPathPrefix()) {
                $prefix = $resource->getPathPrefix();
            }
            $path = $path->append($prefix)
                ->ensureStart('/')
            ;

            if ($operation instanceof CollectionOperationInterface) {
                $path = $path->append('/', $operation->getShortName());
            }

            if (!$operation instanceof CollectionOperationInterface) {
                $path = $path->ensureEnd('/');

                foreach ($operation->getIdentifiers() ?? [] as $identifier) {
                    $path = $path
                        ->append('{')
                        ->append($identifier)
                        ->append('}')
                        ->append('/')
                    ;
                }

                $path = $path->append($operation->getShortName());
            }
            $operation = $operation->withPath($path->lower()->toString());
        } elseif ($resource->getPathPrefix() !== null) {
            $path = u($operation->getPath())
                ->prepend($resource->getPathPrefix())
            ;
            $operation = $operation->withPath($path->lower()->toString());
        }

        if ($operation->getIdentifiers() !== null && $operation->getPath() !== null && $operation->getRouteParameters() === null) {
            $path = u($operation->getPath());

            if ($path->containsAny('{') && $path->containsAny('}')) {
                $parameters = [];

                foreach ($operation->getIdentifiers() as $identifier => $getter) {
                    if (is_numeric($identifier)) {
                        $identifier = $getter;
                    }
                    $parameters[$identifier] = $getter;
                }
                $operation = $operation->withRouteParameters($parameters);
            }
        }
        $redirectRoute = $operation->getRedirectRoute();

        if ($redirectRoute === null) {
            if ($resource->hasOperation('index')) {
                $redirectRoute = $this->routeNameGenerator->generateRouteName($resource, $resource->getOperation('index'));
            } elseif ($resource->hasOperation('update')) {
                $redirectRoute = $this->routeNameGenerator->generateRouteName($resource, $resource->getOperation('update'));
            }
        }

        return $operation
            ->withRouteParameters($operation->getRouteParameters() ?? [])
            ->withRedirectRoute($redirectRoute)
        ;
    }
}
