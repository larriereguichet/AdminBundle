<?php

namespace LAG\AdminBundle\Slug\Generator;

use Symfony\Component\String\Slugger\AsciiSlugger;
use function Symfony\Component\String\u;

class SimpleSlugGenerator implements SlugGeneratorInterface
{
    public function generateSlug(string $source, string $generatorName = 'default'): string
    {
        return (new AsciiSlugger())->slug(u($source)->lower()->toString())->toString();
    }
}
