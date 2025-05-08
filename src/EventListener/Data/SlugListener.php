<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Event\DataEvent;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Slug;
use LAG\AdminBundle\Slug\Registry\SluggerRegistryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

final readonly class SlugListener
{
    public function __construct(
        private SluggerRegistryInterface $registry,
    ) {
    }

    public function __invoke(DataEvent $event): void
    {
        $data = $event->getData();
        $resource = $event->getResource();

        if (!$this->supports($resource)) {
            return;
        }
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($resource->getProperties() as $property) {
            if ($property instanceof Slug) {
                $source = $accessor->getValue($data, $property->getSource());
                $slugger = $this->registry->get($property->getSlugger());
                $accessor->setValue($data, $property->getPropertyPath(), $slugger->generateSlug($source));
            }
        }
    }

    private function supports(Resource $resource): bool
    {
        foreach ($resource->getProperties() as $property) {
            if ($property instanceof Slug) {
                return true;
            }
        }

        return false;
    }
}
