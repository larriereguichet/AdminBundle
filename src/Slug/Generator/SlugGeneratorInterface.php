<?php

namespace LAG\AdminBundle\Slug\Generator;

use LAG\AdminBundle\Entity\Mapping\Sluggable;

interface SlugGeneratorInterface
{
    public function generateSlug(string $source, string $generatorName = 'default'): string;
}
