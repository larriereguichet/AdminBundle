<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Slug\Slugger;

use Symfony\Component\String\Slugger\AsciiSlugger;

final readonly class DefaultSlugger implements DefaultSluggerInterface
{
    public function generateSlug(string $source): string
    {
        return (new AsciiSlugger())->slug($source)->lower()->toString();
    }
}
