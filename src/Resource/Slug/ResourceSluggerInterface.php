<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Slug;

interface ResourceSluggerInterface
{
    /** @param string|string[] $sourceProperties */
    public function generateSlug(object $resource, string|array $sourceProperties, string $targetProperty): string;
}
