<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Metadata\Attribute\Slug;
use LAG\AdminBundle\Resource\Slug\ResourceSluggerInterface;

/**
 * Generate a slug for a resource property from a resource property source.
 */
final readonly class GenerateSlugListener
{
    public function __construct(
        private ResourceSluggerInterface $slugger,
    ) {
    }

    public function __invoke(DataEvent $event): void
    {
        $data = $event->getData();
        $resource = $event->getResource();

        foreach ($resource->getPropertiesByType(Slug::class) as $property) {
            $this->slugger->generateSlug($data, $property->getSource(), $property->getPropertyPath());
        }
    }
}
