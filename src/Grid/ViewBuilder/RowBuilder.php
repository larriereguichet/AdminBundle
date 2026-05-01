<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Grid\View\RowView;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;

final readonly class RowBuilder implements RowBuilderInterface
{
    public function __construct(
        private CellBuilderInterface $cellBuilder,
        private HeaderBuilderInterface $headerBuilder,
        private LinkBuilderInterface $linkBuilder,
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function buildHeadersRow(CollectionOperationInterface $operation, GridInterface $grid, array $context = []): RowView
    {
        $headers = [];

        foreach ($grid->getProperties() as $propertyName) {
            $property = $operation->getResource()->getProperty($propertyName);
            $headers[] = $this->headerBuilder->buildHeader($operation, $grid, $property, $context);
        }

        if (\count($operation->getItemLinks()) > 0) {
            $headers[] = new HeaderView(name: 'links', attributes: $this->attributeBuilder->buildAttributes([]));
        }

        return new RowView(
            attributes: $this->attributeBuilder->buildAttributes($grid->getHeaderRowAttributes()),
            cells: $headers,
        );
    }

    /** @param array<string, mixed> $context */
    public function buildRow(CollectionOperationInterface $operation, GridInterface $grid, mixed $data, array $context = []): RowView
    {
        $cells = [];
        $context['row_data'] = $data;

        foreach ($grid->getProperties() as $propertyName) {
            $property = $operation->getResource()->getProperty($propertyName);
            $cells[] = $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
        }

        foreach ($operation->getItemLinks() ?? [] as $link) {
            $link = $this->linkBuilder->buildLink($link, $data, $context);

            if ($link !== null) {
                $cells[] = $link;
            }
        }

        return new RowView(
            attributes: $this->attributeBuilder->buildAttributes($grid->getRowAttributes()),
            cells: $cells,
            data: $data,
        );
    }
}
