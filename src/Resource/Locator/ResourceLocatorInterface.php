<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Metadata\Resource;

interface ResourceLocatorInterface
{
    /** @return iterable<int, Resource> */
    public function locateResources(\ReflectionClass $resourceClass): iterable;
}
