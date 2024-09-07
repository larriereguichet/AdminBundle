<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\CompoundPropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\Metadata\Resource as AdminResource;

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

    public function buildCell(Grid $grid, PropertyInterface $property, mixed $data, array $context = []): CellView
    {
        if (!$property instanceof CompoundPropertyInterface || !empty($context['children'])) {
            return $this->cellBuilder->buildCell($grid, $property, $data, $context);
        }
        /** @var AdminResource $resource */
        $resource = $context['resource'];
        $children = [];

        foreach ($property->getProperties() as $childPropertyName) {
            $child = $resource->getProperty($childPropertyName);
            $children[] = $this->cellBuilder->buildCell($grid, $child, $data);
        }
        $context['children'] = $children;

        return $this->cellBuilder->buildCell($grid, $property, $data, $context);
    }
}
