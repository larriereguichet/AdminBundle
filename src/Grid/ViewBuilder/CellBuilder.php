<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;

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
    ): CellView {
        return new CellView(
            name: $property->getName(),
            attributes: $this->attributeBuilder->buildAttributes($property->getAttributes()),
            property: $property,
            template: $property->getTemplate(),
            component: $property->getComponent(),
            data: $data,
            context: $context,
        );
    }
}
