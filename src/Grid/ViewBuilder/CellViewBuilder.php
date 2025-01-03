<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class CellViewBuilder implements CellViewBuilderInterface
{
    public function buildCell(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): CellView {
        return new CellView(
            name: $property->getName(),
            options: $property,
            template: $property->getTemplate(),
            data: $data,
            attributes: $property->getAttributes(),
            rowAttributes: $property->getRowAttributes(),
            context: $context,
        );
    }
}
