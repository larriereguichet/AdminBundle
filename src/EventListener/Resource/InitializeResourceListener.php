<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Resource;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Resource\Registry\ApplicationRegistryInterface;
use Symfony\Component\String\Inflector\EnglishInflector;

use function Symfony\Component\String\u;

final readonly class InitializeResourceListener
{
    public function __construct(
        private ?string $application,
        private ?string $translationDomain,
        private ApplicationRegistryInterface $registry,
    ) {
    }

    public function __invoke(ResourceEvent $event): void
    {
        $resource = $event->getResource();
        $application = null;

        if ($resource->getApplication() === null) {
            $resource = $resource->withApplication($this->application);
        }

        if ($resource->getApplication() !== null && $this->registry->has($resource->getApplication())) {
            $application = $this->registry->get($resource->getApplication());
        }

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
            if ($application?->getTranslationDomain() !== null) {
                $resource = $resource->withTranslationDomain($application->getTranslationDomain());
            } else {
                $resource = $resource->withTranslationDomain($this->translationDomain);
            }
        }

        if ($resource->getTranslationPattern() === null) {
            if ($application?->getTranslationPattern() !== null) {
                $resource = $resource->withTranslationPattern($application->getTranslationPattern());
            } else {
                $resource = $resource->withTranslationPattern('{application}.{resource}.{message}');
            }
        }

        if ($resource->getRoutePattern() === null) {
            if ($application?->getRoutePattern() !== null) {
                $resource = $resource->withRoutePattern($application->getRoutePattern());
            } else {
                $resource = $resource->withRoutePattern('{application}.{resource}.{operation}');
            }
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

        $event->setResource($resource);
    }
}
