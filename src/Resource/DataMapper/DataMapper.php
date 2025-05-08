<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\DataMapper;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\PropertyInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final readonly class DataMapper implements DataMapperInterface
{
    private PropertyAccessorInterface $accessor;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function getValue(PropertyInterface $property, mixed $data): mixed
    {
        if ($property->getPropertyPath() === '.' || $property->getPropertyPath() === true) {
            return $data;
        }

        if ($property->getPropertyPath() === null || $property->getPropertyPath() === false) {
            return null;
        }

        if (!$this->accessor->isReadable($data, $property->getPropertyPath())) {
            throw new Exception(\sprintf('The property path "%s" is not readable in data of type "%s"', $property->getPropertyPath(), get_debug_type($data)));
        }

        return $this->accessor->getValue($data, $property->getPropertyPath());
    }
}
