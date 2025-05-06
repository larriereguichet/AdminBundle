<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Config\Transformer;

final readonly class InheritedPropertyTransformer
{
    public function __invoke(object $object, callable $next): mixed
    {
        $result = $next();

        $reflectionClass = new \ReflectionClass($object);
        $properties = $reflectionClass->getProperties();

        while ($reflectionClass->getParentClass() !== false) {
            $reflectionClass = $reflectionClass->getParentClass();
            $properties = array_merge($properties, $reflectionClass->getProperties());
        }

        foreach ($properties as $property) {
            if (\array_key_exists($property->getName(), $result)) {
                continue;
            }
            $result[$property->getName()] = $property->getValue($object);
        }

        return $result;
    }
}
