<?php

namespace LAG\AdminBundle\EventListener\Resource;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\ResourceLink;
use function Symfony\Component\String\u;

final readonly class InitializeResourcePropertiesListener
{
    public function __invoke(ResourceEvent $event): void
    {
        $resource = $event->getResource();

        // Initialize single properties first as they will be available to build collection properties
        foreach ($resource->getProperties() as $property) {
            $property = $this->initializeProperty($resource, $property);
            $resource = $resource->withProperty($property);
        }
        $event->setResource($resource);
    }

    private function initializeProperty(Resource $resource, PropertyInterface $property): PropertyInterface
    {
        if ($property->getPropertyPath() === null) {
            $property = $property->withPropertyPath($property->getName());
        }

        if ($property->getLabel() === null) {
            if ($resource->getTranslationPattern()) {
                $label = u($resource->getTranslationPattern())
                    ->replace('{application}', $resource->getApplication())
                    ->replace('{resource}', $resource->getName())
                    ->replace('{message}', u($property->getName())->snake()->toString())
                    ->lower()
                    ->toString()
                ;
            } else {
                $label = u($property->getName())
                    ->replace('_', ' ')
                    ->title()
                    ->toString()
                ;
            }
            $property = $property->withLabel($label);
        }

        if (!$property->getTranslationDomain()) {
            $property = $property->withTranslationDomain($resource->getTranslationDomain());
        }

        if ($property instanceof ResourceLink) {
            if ($property->getApplication() === null) {
                $property = $property->withApplication($resource->getApplication());
            }

            if ($property->getResource() === null) {
                $property = $property->withResource($resource->getName());
            }
        }

        return $property;
    }
}