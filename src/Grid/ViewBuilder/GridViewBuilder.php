<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Grid;

final readonly class GridViewBuilder implements GridViewBuilderInterface
{
    public function __construct(
        private GridFactoryInterface $gridFactory,
        private RowViewBuilderInterface $rowBuilder,
        private ActionViewBuilderInterface $actionBuilder,
    ) {
    }

    public function build(
        CollectionOperationInterface $operation,
        mixed $data,
        array $context = [],
    ): GridView {
        $grid = $this->gridFactory->createGrid($operation);

        return new GridView(
            name: $grid->getName(),
            type: $grid->getType(),
            headers: $this->buildHeaders($operation, $grid, $context),
            rows: $this->buildRows($operation, $grid, $data, $context),
            attributes: $grid->getAttributes(),
            title: $grid->getTitle(),
            template: $grid->getTemplate(),
            options: $grid->getOptions(),
            actions: $this->buildCollectionActions($grid, $data),
            context: $context,
            containerAttributes: $grid->getContainerAttributes(),
            actionCellAttributes: $grid->getActionCellAttributes(),
            extraColumn: \count($grid->getActions()) > 0,
            emptyMessage: $grid->getEmptyMessage(),
            translationDomain: $grid->getTranslationDomain(),
        );
    }

    private function buildHeaders(CollectionOperationInterface $operation, Grid $grid, array $context): RowView
    {
        return $this->rowBuilder->buildHeadersRow($operation, $grid, $context);
    }

    private function buildRows(CollectionOperationInterface $operation, Grid $grid, mixed $data, array $context): iterable
    {
        if (!is_iterable($data)) {
            throw new Exception('Data must be iterable to build a grid.');
        }
        $rows = [];

        foreach ($data as $row) {
            $rows[] = $this->rowBuilder->buildRow($operation, $grid, $row, $context);
        }

        return $rows;
    }

    private function buildCollectionActions(Grid $grid, mixed $data): array
    {
        $actionViews = [];
        $actions = $grid->getCollectionActions();

        foreach ($actions as $action) {
            $actionView = $this->actionBuilder->buildActions($action, $data);

            if ($actionView !== null) {
                $actionViews[] = $actionView;
            }
        }

        return $actionViews;
    }
}
