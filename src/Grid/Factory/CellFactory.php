<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Grid\Cell;
use LAG\AdminBundle\Resource\Metadata\ConfigurablePropertyInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\Metadata\TransformablePropertyInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class CellFactory implements CellFactoryInterface
{
    private PropertyAccessor $accessor;

    public function __construct()
    {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function create(PropertyInterface $property, mixed $data): Cell
    {
        if ($property instanceof ConfigurablePropertyInterface) {
            $property->configure($data);
        }

        if ($property->getPropertyPath() !== null && $property->getPropertyPath() !== '.' && $this->accessor->isReadable($data, $property->getPropertyPath())) {
            $data = $this->accessor->getValue($data, $property->getPropertyPath());
        }

        if ($property instanceof TransformablePropertyInterface) {
            $data = $property->transform($data);
        }

        return new Cell($property->getTemplate(), ['options' => $property, 'data' => $data]);
    }
}
