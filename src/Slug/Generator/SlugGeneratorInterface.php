<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Slug\Generator;

interface SlugGeneratorInterface
{
    public function generateSlug(string $source, string $generatorName = 'default'): string;
}
