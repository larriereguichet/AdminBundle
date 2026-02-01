<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Grid\View\Header;
use LAG\AdminBundle\Grid\View\Row;

final readonly class RowViewBuilder implements RowViewBuilderInterface
{
    public function __construct(
        private CellBuilderInterface $cellBuilder,
        private ActionBuilderInterface $actionsBuilder,
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function buildHeadersRow(OperationInterface $operation, GridInterface $grid, array $context = []): Row
    {
        $headers = [];

        foreach ($grid->getProperties() as $propertyName) {
            $property = $operation->getResource()->getProperty($propertyName);
            $headers[] = $this->cellBuilder->buildHeader($operation, $grid, $property, $context);
        }

        if (\count($grid->getActions()) > 0) {
            $headers[] = new Header(name: 'actions');
        }

        return new Row(
            cells: $headers,
            attributes: $this->attributeBuilder->buildAttributes($grid->getHeaderRowAttributes()),
        );
    }

    /** @param array<string, mixed> $context */
    public function buildRow(OperationInterface $operation, GridInterface $grid, mixed $data, array $context = []): Row
    {
        $cells = [];
        $context['row_data'] = $data;

        foreach ($grid->getProperties() as $propertyName) {
            $property = $operation->getResource()->getProperty($propertyName);
            $cells[] = $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
        }
        $actions = [];

        foreach ($grid->getActions() as $action) {
            $action = $this->actionsBuilder->buildAction($action, $data, $context);

            if ($action !== null) {
                $actions[] = $action;
            }
        }

        return new Row(
            cells: $cells,
            actions: $actions,
            data: $data,
            attributes: $this->attributeBuilder->buildAttributes($grid->getRowAttributes()),
        );
    }
}
