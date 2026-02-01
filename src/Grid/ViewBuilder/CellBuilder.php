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

    /** @param array<string, mixed> $context */
    public function buildHeader(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        array $context = []
    ): Header {
        return new Header(
            name: $property->getName(),
            attributes: $this->attributeBuilder->buildAttributes($property->getHeaderAttributes()),
            label: $property->getLabel(),
            translationDomain: $grid->getTranslationDomain(),
            sort: $context['sort'] ?? null,
            sortParameter: '', // TODO ?
            order: $context['order'] ?? null,
            orderParameter: '',
            sortable: $grid->isSortable() && $property->isSortable(),
        );
    }
}
