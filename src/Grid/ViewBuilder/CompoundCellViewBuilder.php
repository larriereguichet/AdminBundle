<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\CompoundPropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

/**
 * Build the cell view for the compound properties. Each child should be build separately and passed to the parent cell
 * context.
 */
final readonly class CompoundCellViewBuilder implements CellViewBuilderInterface
{
    public function __construct(
        private CellViewBuilderInterface $cellBuilder,
    ) {
    }

    public function buildCell(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): CellView {
        if (!$property instanceof CompoundPropertyInterface || !empty($context['children'])) {
            return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
        }
        $children = [];

        foreach ($property->getProperties() as $childPropertyName) {
            $child = $operation->getResource()->getProperty($childPropertyName);
            $children[] = $this->cellBuilder->buildCell($operation, $grid, $child, $data);
        }
        $context['children'] = $children;

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
