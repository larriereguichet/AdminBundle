<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Slug;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class ResourceSlugger implements ResourceSluggerInterface
{
    public function __construct(
        private SluggerInterface $slugger,
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    /** @param string|string[] $sourceProperties */
    public function generateSlug(object $resource, string|array $sourceProperties, string $targetProperty): string
    {
        $parts = array_map(
            fn (string $property) => (string) $this->propertyAccessor->getValue($resource, $property),
            (array) $sourceProperties,
        );
        $slug = $this->slugger->slug(implode(' ', $parts))->toString();
        $this->propertyAccessor->setValue($resource, $targetProperty, $slug);

        return $slug;
    }
}
