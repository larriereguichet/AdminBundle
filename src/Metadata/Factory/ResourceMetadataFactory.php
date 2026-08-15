<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\Resource\MissingResourceException;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class ResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceCollectionMetadataFactoryInterface $collectionMetadataFactory,
        private ApplicationMetadataFactoryInterface $applicationFactory,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $resources = $this->collectionMetadataFactory->createMetadata();

        if (!\array_key_exists($resourceName, $resources)) {
            throw new MissingResourceException($resourceName);
        }
        $resource = $resources[$resourceName];

        if ($resource->getResourceClass() === null) {
            throw new Exception('The resource class is missing for the resource "%s"', $resourceName);
        }
        $application = $this->applicationFactory->createMetadata($resource->getApplication());
        $title = u(new EnglishInflector()->pluralize($resource->getShortName())[0])
            ->replace('_', ' ')
            ->title()
            ->trim()
            ->toString()
        ;

        return $resource
            ->withTitle($resource->getTitle() ?? $title)
            ->withTranslationDomain($resource->getTranslationDomain() ?? $application->getTranslationDomain())
            ->withTranslationPattern($resource->getTranslationPattern() ?? $application->getTranslationPattern())
            ->withRoutePattern($resource->getRoutePattern() ?? $application->getRoutePattern())
            ->withPermissions($resource->getPermissions() ?? [])
            ->withNormalizationContext($resource->getNormalizationContext() ?? [])
            ->withDenormalizationContext($resource->getDenormalizationContext() ?? [])
            ->withFormOptions($resource->getFormOptions() ?? [])
        ;
    }
}
