<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class OperationRoutingInitializer implements OperationRoutingInitializerInterface
{
    public function __construct(
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function initializeOperationRouting(Application $application, OperationInterface $operation): OperationInterface
    {
        $resource = $operation->getResource();

        if ($resource === null) {
            throw new Exception('The resource should be initialized');
        }

        if ($operation->getRoute() === null) {
            $route = $this->routeNameGenerator->generateRouteName($resource, $operation);
            $operation = $operation->withRoute($route);
        }
        $identifiers = $operation->getIdentifiers();

        if (empty($identifiers)) {
            $identifiers = $resource->getIdentifiers();
        }

        if ($operation->getPath() === null) {
            $path = u();
            $inflector = new EnglishInflector();
            $prefix = $inflector->pluralize(u($resource->getName())->lower()->toString())[0];

            if ($resource->getPathPrefix()) {
                $prefix = $resource->getPathPrefix();
            }
            $path = $path->append($prefix)
                ->ensureStart('/')
            ;

            if ($operation instanceof CollectionOperationInterface) {
                $path = $path->append('/', $operation->getName());
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

                $path = $path->append($operation->getName());
            }
            $operation = $operation->withPath($path->lower()->toString());
        } elseif ($resource->getPathPrefix() !== null) {
            $path = u($operation->getPath())
                ->prepend($resource->getPathPrefix())
            ;
            $operation = $operation->withPath($path->lower()->toString());
        }

        if ($identifiers !== null && $operation->getPath() !== null && $operation->getRouteParameters() === null) {
            $path = u($operation->getPath());

            if ($path->containsAny('{') && $path->containsAny('}')) {
                $parameters = [];

                foreach ($identifiers as $identifier => $getter) {
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
