<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class GridBuilder implements GridBuilderInterface
{
    public function __construct(
        private RowBuilderInterface $rowBuilder,
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    public function build(
        GridInterface $grid,
        CollectionOperationInterface $operation,
        iterable $data,
        array $context = [],
    ): View\GridView {
        $headers = null;

        if ($grid->useHeaders()) {
            $headers = $this->rowBuilder->buildHeadersRow($operation, $grid, $context);
        }
        $context['translation_domain'] = $operation->getResource()->getTranslationDomain();

        return new View\GridView(
            name: $grid->getName(),
            type: $grid->getType(),
            rows: $this->buildRows($operation, $grid, $data, $context),
            attributes: $this->attributeBuilder->buildAttributes($grid->getAttributes()),
            headers: $headers,
            title: $this->buildTitle($grid, $operation),
            template: $grid->getTemplate(),
            options: $grid->getOptions(),
            context: $context,
            emptyMessage: $grid->getEmptyMessage(),
            translationDomain: $operation->getResource()->getTranslationDomain(),
        );
    }

    /**
     * @param iterable<int|string, mixed> $data
     * @param array<string, mixed> $context
     *
     * @return iterable<int, View\RowView>
     */
    private function buildRows(CollectionOperationInterface $operation, GridInterface $grid, iterable $data, array $context): iterable
    {
        $rows = [];

        foreach ($data as $row) {
            $rows[] = $this->rowBuilder->buildRow($operation, $grid, $row, $context);
        }

        return $rows;
    }

    private function buildTitle(GridInterface $grid, OperationInterface $operation): ?View\TitleView
    {
        if ($grid->getTitle() === null) {
            return null;
        }

        return new View\TitleView(
            title: $grid->getTitle(),
            attributes: $this->attributeBuilder->buildAttributes($grid->getTitleAttributes()),
            translationDomain: $operation->getResource()->getTranslationDomain(),
        );
    }
}
