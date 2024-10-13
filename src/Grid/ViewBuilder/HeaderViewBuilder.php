<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class HeaderViewBuilder implements HeaderViewBuilderInterface
{
    public function buildHeader(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        array $context = []
    ): HeaderView {
        if ($property->getLabel() === false) {
            return new HeaderView(
                name: $property->getName(),
                attributes: ['class' => 'border border-0'],
            );
        }

        return new HeaderView(
            name: $property->getName(),
            template: $grid->getHeaderTemplate(),
            label: $property->getLabel() ?: '',
            sortable: $grid->isSortable() && $property->isSortable(),
            translationDomain: $grid->getTranslationDomain(),
            sort: $context['sort'] ?? null,
            order: $context['order'] ?? null,
            attributes: $property->getAttributes(),
        );
    }
}
