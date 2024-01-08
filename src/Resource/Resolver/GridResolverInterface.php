<?php

namespace LAG\AdminBundle\Resource\Resolver;

interface GridResolverInterface
{
    public function resolveGrids(array $directories): iterable;
}
