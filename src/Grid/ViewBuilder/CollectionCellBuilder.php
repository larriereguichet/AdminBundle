<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\View\Header;
use LAG\AdminBundle\Metadata\Attribute\Collection;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;

final readonly class CollectionCellBuilder implements CellBuilderInterface
{
    public function __construct(
        private CellBuilderInterface $cellBuilder,
        private DataMapperInterface $dataMapper,
    ) {
    }

    public function buildHeader(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        array $context = []
    ): Header {
        return $this->cellBuilder->buildHeader($operation, $grid, $property, $context);
    }

    public function buildCell(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = [],
    ): Cell {
        if (!$property instanceof Collection) {
            return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
        }

        if (!is_iterable($data)) {
            throw new Exception(\sprintf('The collection property "%s" requires iterable data, got "%s"', $property->getName(), get_debug_type($data)));
        }
        $context['children'] = [];
        $index = 0;

        foreach ($data as $propertyData) {
            $childProperty = $property
                ->getEntryProperty()
                ->withName($property->getName().'_'.$index)
            ;
            $propertyData = $this->dataMapper->getPropertyValue($propertyData, $childProperty);

            $context['children'][] = $this->cellBuilder->buildCell(
                $operation,
                $grid,
                $childProperty,
                $propertyData,
            );
            ++$index;
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
