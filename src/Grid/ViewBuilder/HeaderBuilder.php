<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
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
    ): HeaderView {
        return new HeaderView(
            name: $property->getName(),
            attributes: $this->attributeBuilder->buildAttributes($property->getHeaderAttributes()),
            label: $property->getLabel() ?: null,
            translationDomain: $context['translation_domain'] ?? 'admin',
            sort: $context['sort'] ?? null,
            sortParameter: $grid->getSortParameter(),
            order: $context['order'] ?? null,
            orderParameter: $grid->getOrderParameter(),
            sortable: $grid->isSortable() && $property->isSortable(),
        );
    }
}
