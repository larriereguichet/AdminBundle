<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Resource;

use LAG\AdminBundle\Event\ResourceEvent;
use Symfony\Component\String\Inflector\EnglishInflector;
use function Symfony\Component\String\u;

readonly class InitializeResourceListener
{
    public function __construct(
        private ?string $applicationName,
        private ?string $translationDomain,
    ) {
    }

    public function __invoke(ResourceEvent $event): void
    {
        $resource = $event->getResource();

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

        if ($resource->getApplicationName() === null) {
            $resource = $resource->withApplicationName($this->applicationName);
        }

        if ($resource->getTranslationDomain() === null) {
            $resource = $resource->withTranslationDomain($this->translationDomain);
        }

        if ($resource->getPermissions() === null) {
            $resource = $resource->withPermissions(['ROLE_ADMIN']);
        }

        if ($resource->getNormalizationContext() === null) {
            $resource = $resource->withNormalizationContext([]);
        }

        if ($resource->getDenormalizationContext() === null) {
            $resource = $resource->withDenormalizationContext([]);
        }

        $event->setResource($resource);
    }
}
