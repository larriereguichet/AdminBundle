<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationMetadataInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class OperationsMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceMetadataFactoryInterface $metadataFactory,
        private ApplicationMetadataFactoryInterface $applicationMetadataFactory,
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $resource = $this->metadataFactory->createMetadata($resourceName);
        $application = $this->applicationMetadataFactory->createMetadata($resource->getApplication());
        $operations = [];
        $inflector = new EnglishInflector();

        foreach ($resource->getOperations() as $operation) {
            $shortName = (string) u($resource->getApplication())
                ->append('.', $resource->getShortName())
                ->append('.', $operation->getShortName())
                ->lower()
            ;

            if ($operation instanceof CollectionOperationInterface) {
                $title = u($inflector->pluralize($resource->getShortName())[0]);
            } else {
                $title = u($operation->getShortName())
                    ->append(' ')
                    ->append($resource->getShortName())
                ;
            }
            $title = (string) $title->replace('_', ' ')->title()->trim();
            $route = $this->routeNameGenerator->generateRouteName($resource, $operation);
            $identifiers = [];

            if ($operation->getRedirectOperation() !== null && !u($operation->getRedirectOperation())->containsAny('.')) {
                $redirectOperation = $application->getName().'.'.$resource->getShortName().'.'.$operation->getRedirectOperation();
            }

            // A create operation has no identifier to route on, as the resource does not exist yet
            if (!$operation instanceof Create) {
                $identifiers = $operation->getIdentifiers() ?? $resource->getIdentifiers() ?? [];
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

                    foreach ($identifiers as $identifier) {
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
            $redirectRoute = null;

            foreach ($resource->getOperations() as $op) {
                if ($op instanceof Index) {
                    $redirectRoute = $this->routeNameGenerator->generateRouteName($resource, $op);
                    break;
                }
            }
            $operation->setResource($resource);
            $operation = $operation
                ->withShortName($operation->getShortName() ?? $shortName)
                ->withTitle($operation->getTitle() ?? $title)
                ->withBaseTemplate($operation->getBaseTemplate() ?? $application->getBaseTemplate())
                ->withRedirectRouteParameters($operation->getRedirectRouteParameters() ?? [])
                ->withRedirectOperation($operation->getRedirectOperation() ?? $redirectOperation ?? null)
                ->withValidation($operation->hasValidation() ?? $resource->hasValidation())
                ->withValidationContext($operation->getValidationContext() ?? $resource->getValidationContext() ?? [])
                ->withAjax($operation->hasAjax() ?? $resource->hasAjax())
                ->withNormalizationContext($operation->getNormalizationContext() ?? $resource->getNormalizationContext() ?? [])
                ->withDenormalizationContext($operation->getDenormalizationContext() ?? $resource->getDenormalizationContext() ?? [])
                ->withPermissions($operation->getRoles() ?? $resource->getPermissions() ?? [])
                ->withIdentifiers($operation->getIdentifiers() ?? $identifiers)
                ->withRoute($operation->getRoute() ?? $route)
                ->withRedirectRoute($operation->getRedirectRoute() ?? $redirectRoute)
                ->withContext(array_merge($resource->getContext(), $operation->getContext()))
            ;
            // Should be done after assignments as it can require identifiers
            $operation = $operation->withRouteParameters(
                $operation->getRouteParameters() ?? $this->generateRouteParametersFromIdentifiers($operation)
            );

            $operations[$operation->getName()] = $operation;
        }

        return $resource->withOperations($operations);
    }

    /** @return array<string, mixed> */
    private function generateRouteParametersFromIdentifiers(OperationMetadataInterface $operation): array
    {
        if ($operation->getIdentifiers() === null || $operation->getPath() === null) {
            return [];
        }
        $routeParameters = [];
        $path = u($operation->getPath());

        if ($path->containsAny('{') && $path->containsAny('}')) {
            foreach ($operation->getIdentifiers() as $identifier => $property) {
                if (is_numeric($identifier)) {
                    $identifier = $property;
                }
                $routeParameters[$identifier] = $property;
            }
        }

        return $routeParameters;
    }
}
