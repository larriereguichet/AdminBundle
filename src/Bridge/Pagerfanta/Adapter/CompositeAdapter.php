<?php

namespace LAG\AdminBundle\Bridge\Pagerfanta\Adapter;

use Pagerfanta\Adapter\AdapterInterface;

class CompositeAdapter implements AdapterInterface
{
    public function __construct(
        private AdapterInterface $adapter,
        private iterable $data,
    )
    {
    }

    public function getNbResults(): int
    {
        // TODO: Implement getNbResults() method.
    }

    public function getSlice(int $offset, int $length): iterable
    {
        // TODO: Implement getSlice() method.
    }
}
