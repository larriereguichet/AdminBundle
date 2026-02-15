<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Grid\View;

final readonly class GridBuilder implements GridBuilderInterface
{
    public function __construct(
        private RowBuilderInterface $rowBuilder,
        private ActionBuilderInterface $actionBuilder,
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    public function build(
        GridInterface $grid,
        CollectionOperationInterface $operation,
        iterable $data,
        array $context = [],
    ): View\Grid {
        $headers = null;

        if ($grid->useHeaders()) {
            $headers = $this->rowBuilder->buildHeadersRow($operation, $grid, $context);
        }

        return new View\Grid(
            name: $grid->getName(),
            type: $grid->getType(),
            rows: $this->buildRows($operation, $grid, $data, $context),
            attributes: $this->attributeBuilder->buildAttributes($grid->getAttributes()),
            headers: $headers,
            title: $this->buildTitle($grid, $operation),
            template: $grid->getTemplate(),
            options: $grid->getOptions(),
            actions: $this->buildCollectionActions($grid, $data, $context),
            context: $context,
            emptyMessage: $grid->getEmptyMessage(),
            translationDomain: $grid->getTranslationDomain(),
        );
    }

    /**
     * @param iterable<int|string, mixed> $data
     * @param array<string, mixed> $context
     *
     * @return iterable<int, View\Row>
     */
    private function buildRows(CollectionOperationInterface $operation, GridInterface $grid, iterable $data, array $context): iterable
    {
        $rows = [];

        foreach ($data as $row) {
            $rows[] = $this->rowBuilder->buildRow($operation, $grid, $row, $context);
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<int, View\Cell>
     */
    private function buildCollectionActions(GridInterface $grid, mixed $data, array $context): array
    {
        $actionViews = [];
        $actions = $grid->getCollectionActions();

        foreach ($actions as $action) {
            $actionView = $this->actionBuilder->buildAction($action, $data, $context);

            if ($actionView !== null) {
                $actionViews[] = $actionView;
            }
        }

        return $actionViews;
    }

    private function buildTitle(GridInterface $grid, OperationInterface $operation): ?View\Title
    {
        if ($grid->getTitle() === null) {
            return null;
        }

        return new View\Title(
            title: $grid->getTitle(),
            attributes: $this->attributeBuilder->buildAttributes($grid->getTitleAttributes()),
            translationDomain: $operation->getResource()->getTranslationDomain(),
        );
    }
}
