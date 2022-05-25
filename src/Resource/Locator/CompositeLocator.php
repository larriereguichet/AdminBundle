<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Admin;

class CompositeLocator implements ResourceLocatorInterface
{
    public function __construct(
        private readonly iterable $locators,
    ) {
    }

    public function locate(string $resourceDirectory): array
    {
        $resources = [];

        foreach ($this->locators as $locator) {
            foreach ($locator->locate($resourceDirectory) as $resource) {
                if (!$resource instanceof Admin) {
                    throw new Exception(sprintf(
                        'The locator "%s" returns an instance of "%s", expected an instance of "%s"',
                        get_class($locator),
                        get_class($resource),
                        Admin::class,
                    ));
                }
                $resources[] = $resource;
            }
        }

        return $resources;
    }
}
