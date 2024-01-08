<?php

namespace LAG\AdminBundle\Resource\Locator;

interface PropertyLocatorInterface
{
    public function locateProperties(\ReflectionClass $resourceClass): array;
}
