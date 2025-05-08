<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Locator;

interface PropertyLocatorInterface
{
    /**
     * Return available properties for the given resource class.
     *
     * @param class-string $resourceClass
     * @return array
     */
    public function locateProperties(string $resourceClass): iterable;
}
