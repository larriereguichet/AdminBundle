<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

// TODO remove
class AttributesHelper
{
    public static function getAttributes(string $sourceClass, string $attributeClass): array
    {
        $reflectionClass = new \ReflectionClass($sourceClass);
        $attributes = [];

        foreach ($reflectionClass->getAttributes($attributeClass) as $attribute) {
            $attributes[] = $attribute->newInstance();
        }

        return $attributes;
    }
}
