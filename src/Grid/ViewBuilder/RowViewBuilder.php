<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\Resource;

final readonly class RowViewBuilder implements RowViewBuilderInterface
{
    public function __construct(
        private CellViewBuilderInterface $cellBuilder,
        private HeaderViewBuilderInterface $headerBuilder,
        private ActionViewBuilderInterface $actionsBuilder,
    ) {
    }

    public function buildHeadersRow(Grid $grid, Resource $resource, array $context): RowView
    {
        $headers = [];

        foreach ($grid->getProperties() as $propertyName) {
            $property = $resource->getProperty($propertyName);
            $headers[] = $this->headerBuilder->buildHeader($grid, $property, $context);
        }

        return new RowView(
            cells: $headers,
            attributes: $grid->getHeaderRowAttributes(),
        );
    }

    public function buildRow(Grid $grid, Resource $resource, mixed $data, array $context): RowView
    {
        $cells = [];
        $context['row_data'] = $data;

        foreach ($grid->getProperties() as $propertyName) {
            $property = $resource->getProperty($propertyName);
            $cells[] = $this->cellBuilder->buildCell($grid, $property, $data, $context);
        }
        $actions = [];

        foreach ($grid->getActions() as $action) {
            $action = $this->actionsBuilder->buildActions($action, $data, $context);

            if ($action !== null) {
                $actions[] = $action;
            }
        }

        return new RowView(
            cells: $cells,
            actions: $actions,
            attributes: $grid->getRowAttributes(),
        );
    }
}
