<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Slug\Slugger;

interface SluggerInterface
{
    public function generateSlug(string $source): string;
}
