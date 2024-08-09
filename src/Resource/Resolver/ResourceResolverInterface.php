<?php

namespace LAG\AdminBundle\Resource\Resolver;

use LAG\AdminBundle\Resource\Metadata\Resource;

interface ResourceResolverInterface
{
    /** @return iterable<int, Resource> */
    public function resolveResources(array $directories): iterable;
}
