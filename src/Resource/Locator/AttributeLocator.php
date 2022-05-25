<?php

namespace LAG\AdminBundle\Resource\Locator;

use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Metadata\AttributesHelper;

class AttributeLocator implements ResourceLocatorInterface
{
    public function locate(string $resourceDirectory): iterable
    {
        $classes = AttributesHelper::getReflectionClassesFromDirectories($resourceDirectory);
        $resources = [];

        foreach ($classes as $reflectionClass) {
            $attributes = $reflectionClass->getAttributes(Admin::class);

            foreach ($attributes as $attribute) {
                $resources[] = $attribute->newInstance();
            }
        }

        return $resources;
    }
}
