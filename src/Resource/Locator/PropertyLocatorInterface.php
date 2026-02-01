<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Metadata\PropertyInterface;

interface PropertyLocatorInterface
{
    /**
     * Return available properties for the given resource class.
     *
     * @param class-string $resourceClass
     *
     * @return iterable<PropertyInterface>
     */
    public function locateProperties(string $resourceClass): iterable;
}
