<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Resolver;

interface GridResolverInterface
{
    public function resolveGrids(array $directories): iterable;
}
