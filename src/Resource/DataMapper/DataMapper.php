<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\DataMapper;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\PropertyInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final readonly class DataMapper implements DataMapperInterface
{
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function getPropertyValue(PropertyInterface $property, mixed $data): mixed
    {
        if ($property->getPropertyPath() === '.' || $property->getPropertyPath() === true) {
            return $data;
        }

        if ($property->getPropertyPath() === null || $property->getPropertyPath() === false) {
            return null;
        }

        if (!$this->propertyAccessor->isReadable($data, $property->getPropertyPath())) {
            throw new Exception(
                'The property path "%s" is not readable in data of type "%s"',
                $property->getPropertyPath(),
                get_debug_type($data),
            );
        }

        return $this->propertyAccessor->getValue($data, $property->getPropertyPath());
    }
}
