<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Slug;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final readonly class ResourceSlugger implements ResourceSluggerInterface
{
    public function __construct(
        private \Symfony\Component\String\Slugger\SluggerInterface $slugger,
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function generateSlug(object $resource, string $sourceProperty, string $targetProperty): string
    {
        $slugSource = $this->propertyAccessor->getValue($resource, $sourceProperty);
        $slug = $this->slugger->slug($slugSource)->toString();
        $this->propertyAccessor->setValue($resource, $targetProperty, $slug);

        return $slug;
    }
}
