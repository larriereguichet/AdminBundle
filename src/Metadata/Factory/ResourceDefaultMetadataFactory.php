<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class ResourceDefaultMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceMetadataFactoryInterface $metadataFactory,
        private ApplicationMetadataFactoryInterface $applicationFactory,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $resource = $this->metadataFactory->createMetadata($resourceName);
        $application = $this->applicationFactory->createMetadata($resource->getApplicationName());

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
