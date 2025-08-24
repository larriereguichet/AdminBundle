<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class RowViewBuilder implements RowViewBuilderInterface
{
    public function __construct(
        private CellViewBuilderInterface $cellBuilder,
        private HeaderViewBuilderInterface $headerBuilder,
        private ActionViewBuilderInterface $actionsBuilder,
    ) {
    }

    public function buildHeadersRow(OperationInterface $operation, Grid $grid, array $context): RowView
    {
        $headers = [];

        foreach ($grid->getProperties() as $propertyName) {
            $property = $operation->getResource()->getProperty($propertyName);
            $headers[] = $this->headerBuilder->buildHeader($operation, $grid, $property, $context);
        }

        if (\count($grid->getActions()) > 0) {
            $headers[] = new HeaderView(name: 'actions');
        }

        return new RowView(
            cells: $headers,
            attributes: $grid->getHeaderRowAttributes(),
        );
    }

    public function buildRow(OperationInterface $operation, Grid $grid, mixed $data, array $context): RowView
    {
        $cells = [];
        $context['row_data'] = $data;

        foreach ($grid->getProperties() as $propertyName) {
            $property = $operation->getResource()->getProperty($propertyName);
            $cells[] = $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
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
            data: $data,
            attributes: $grid->getRowAttributes(),
        );
    }
}
