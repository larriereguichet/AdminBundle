<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class HeaderViewBuilder implements HeaderViewBuilderInterface
{
    public function buildHeader(Grid $grid, PropertyInterface $property, array $context = []): HeaderView
    {
        return new HeaderView(
            name: $property->getName(),
            template: $grid->getHeaderTemplate(),
            label: $property->getLabel() ?: '',
            sortable: $property->isSortable(),
            translationDomain: $grid->getTranslationDomain(),
            attributes: $property->getAttributes(),
        );
    }
}
