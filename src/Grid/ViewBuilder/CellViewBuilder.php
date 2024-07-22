<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class CellViewBuilder implements CellViewBuilderInterface
{
    public function __construct(
        private DataTransformerRegistryInterface $dataTransformerRegistry,
    ) {
    }

    public function buildHeader(Grid $grid, PropertyInterface $property, array $context = []): HeaderView
    {
        return new HeaderView(
            name: $property->getName(),
            label: $property->getLabel() ?: '',
            sortable: $property->isSortable(),
            translationDomain: $grid->getTranslationDomain(),
            attributes: $property->getAttributes(),
        );
    }

    public function build(Grid $grid, PropertyInterface $property, mixed $data, array $context = []): CellView
    {
        if ($property->getDataTransformer() !== null) {
            $dataTransformer = $this->dataTransformerRegistry->get($property->getDataTransformer());
            $data = $dataTransformer->transform($property, $data);
        }

        return new CellView(
            name: $property->getName(),
            options: $property,
            data: $data,
            template: $property->getTemplate(),
            attributes: $property->getAttributes(),
            containerAttributes: $property->getContainerAttributes(),
            context: $context,
        );
    }
}
