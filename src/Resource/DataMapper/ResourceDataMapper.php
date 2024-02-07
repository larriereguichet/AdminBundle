<?php

namespace LAG\AdminBundle\Resource\DataMapper;

use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ResourceDataMapper
{
    public static function mapData(mixed $data, string|bool|null $propertyPath): mixed
    {
        return match ($propertyPath) {
            true, '.' => $data,
            null, false => null,
            default => self::mapPropertyData($data, $propertyPath),
        };
    }

    public static function mapPropertyData(mixed $data, string $propertyPath): mixed
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        if (!$accessor->isReadable($data, $propertyPath)) {
            throw new Exception(sprintf('The property path "%s" is not readable', $propertyPath));
        }

        return $accessor->getValue($data, $propertyPath);
    }
}
