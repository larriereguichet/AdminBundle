<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\Header;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;

final readonly class HeaderBuilder implements HeaderBuilderInterface
{
    public function __construct(
        private AttributeBuilderInterface $attributeBuilder,
    ) {
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
