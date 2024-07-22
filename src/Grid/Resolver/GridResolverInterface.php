<?php

namespace LAG\AdminBundle\Grid\Resolver;

interface GridResolverInterface
{
    public function resolveGrids(array $directories): iterable;
}
