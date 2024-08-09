<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Slug\Registry;

use LAG\AdminBundle\Slug\Slugger\SluggerInterface;

interface SluggerRegistryInterface
{
    public function get(string $name): SluggerInterface;

    /** @return iterable<int, SluggerInterface> */
    public function all(): iterable;
}
