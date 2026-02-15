<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Grid\View\Header;

final readonly class CellBuilder implements CellBuilderInterface
{
    public function __construct(
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function buildCell(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = [],
    ): Cell {
        return new Cell(
            name: $property->getName(),
            attributes: $this->attributeBuilder->buildAttributes($property->getAttributes()),
            property: $property,
            template: $property->getTemplate(),
            data: $data,
            context: $context,
        );
    }
}
