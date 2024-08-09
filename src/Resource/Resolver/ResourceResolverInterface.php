<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Resolver;

use LAG\AdminBundle\Resource\Metadata\Resource;

interface ResourceResolverInterface
{
    /** @return iterable<int, resource> */
    public function resolveResources(array $directories): iterable;
}
