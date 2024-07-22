<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Resource\Metadata\Resource;

interface ResourceLocatorInterface
{
    /** @return iterable<int, Resource> */
    public function locateResources(\ReflectionClass $resourceClass): iterable;
}
