<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Slug\Registry;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Slug\Slugger\SluggerInterface;

final readonly class SluggerRegistry implements SluggerRegistryInterface
{
    public function __construct(
        private iterable $sluggers,
    ) {
    }

    public function get(string $name): SluggerInterface
    {
        $sluggers = iterator_to_array($this->sluggers);
        $slugger = $sluggers[$name] ?? null;

        if ($slugger === null) {
            throw new Exception(\sprintf('The slugger "%s" does not exist. Did you add the "lag_admin.slugger" tag ?', $name));
        }

        return $slugger;
    }

    public function all(): iterable
    {
        return $this->sluggers;
    }
}
