<?php

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Grid\Cell;
use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class CellFactory implements CellFactoryInterface
{
    private PropertyAccessor $accessor;

    public function __construct(
        private DataTransformerInterface $dataTransformer,
    ) {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function create(PropertyInterface $property, mixed $data): Cell
    {
        $data = $this->accessor->getValue($data, $property->getPropertyPath());
        $data = $this->dataTransformer->transform($property, $data);

        return new Cell(
            $property->getTemplate(),
            [
                'cell' => $property,
                'data' => $data,
            ],
            $data,
        );
    }
}
