<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\Attribute\Collection;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;

final readonly class CollectionCellBuilder implements CellBuilderInterface
{
    public function __construct(
        private CellBuilderInterface $cellBuilder,
        private DataMapperInterface $dataMapper,
    ) {
    }

    public function buildCell(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = [],
    ): CellView {
        if (!$property instanceof Collection) {
            return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
        }

        if (!is_iterable($data)) {
            throw new Exception(\sprintf('The collection property "%s" requires iterable data, got "%s"', $property->getName(), get_debug_type($data)));
        }
        $context['children'] = [];

        foreach ($data as $propertyData) {
            $propertyData = $this->dataMapper->getPropertyValue($property->getEntryProperty(), $propertyData);

            $context['children'][] = $this->cellBuilder->buildCell(
                $operation,
                $grid,
                $property->getEntryProperty(),
                $propertyData,
            );
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
