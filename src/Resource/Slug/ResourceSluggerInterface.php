<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Slug;

interface ResourceSluggerInterface
{
    public function generateSlug(object $resource, string $sourceProperty, string $targetProperty): string;
}
