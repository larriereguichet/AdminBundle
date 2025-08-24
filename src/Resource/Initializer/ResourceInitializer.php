<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Factory\ApplicationFactoryInterface;
use LAG\AdminBundle\Resource\PropertyGuesser\ResourcePropertyGuesserInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class ResourceInitializer implements ResourceInitializerInterface
{
    public function __construct(
        private ApplicationFactoryInterface $applicationFactory,
        private OperationInitializerInterface $operationInitializer,
        private PropertyInitializerInterface $propertyInitializer,
        private ResourcePropertyGuesserInterface $propertyGuesser,
    ) {
    }

    public function initializeResource(Resource $resource): Resource
    {
        $application = $this->applicationFactory->create($resource->getApplication());

        if ($resource->getTitle() === null) {
            $inflector = new EnglishInflector();
            $title = u($inflector->pluralize($resource->getName())[0])
                ->replace('_', ' ')
                ->title()
                ->trim()
                ->toString()
            ;
            $resource = $resource->withTitle($title);
        }

        if ($resource->getTranslationDomain() === null) {
            $resource = $resource->withTranslationDomain($application->getTranslationDomain());
        }

        if ($resource->getTranslationPattern() === null) {
            $resource = $resource->withTranslationPattern($application->getTranslationPattern() ?? '{application}.{resource}.{message}');
        }

        if ($resource->getRoutePattern() === null) {
            $resource = $resource->withRoutePattern($application->getRoutePattern() ?? '{application}.{resource}.{operation}');
        }

        if ($resource->getPermissions() === null) {
            $resource = $resource->withPermissions([]);
        }

        if ($resource->getNormalizationContext() === null) {
            $resource = $resource->withNormalizationContext([]);
        }

        if ($resource->getDenormalizationContext() === null) {
            $resource = $resource->withDenormalizationContext([]);
        }

        if ($resource->getFormOptions() === null) {
            $resource = $resource->withFormOptions([]);
        }

        if (!$resource->hasProperties()) {
            $properties = $this->propertyGuesser->guessProperties($resource);
            $resource = $resource->withProperties($properties);
        }
        $properties = [];

        foreach ($resource->getProperties() as $property) {
            $properties[] = $this->propertyInitializer->initializeProperty($resource, $property);
        }
        $resource = $resource->withProperties($properties);
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            $operation->setResource($resource);
            $operations[] = $this->operationInitializer->initializeOperation($application, $operation);
        }

        return $resource->withOperations($operations);
    }
}
