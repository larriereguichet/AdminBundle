<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Data;

use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Slug\Generator\SlugGeneratorInterface;
use LAG\AdminBundle\Slug\Mapping\SlugMappingInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SlugListener
{
    public function __construct(
        private SlugGeneratorInterface $generator,
        private SlugMappingInterface $mapping,
    ) {
    }

    public function __invoke(DataEvent $event): void
    {
        $data = $event->getData();

        if (!$this->mapping->hasMapping(get_class($data))) {
            return;
        }
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($this->mapping->getMapping(get_class($data)) as $mapping) {
            $source = $accessor->getValue($data, $mapping->sourceProperty);
            $slug = $this->generator->generateSlug($source, $mapping->generator);
            $accessor->setValue($data, $mapping->targetProperty, $slug);
        }
    }
}
