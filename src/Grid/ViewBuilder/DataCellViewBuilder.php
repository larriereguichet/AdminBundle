<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class DataCellViewBuilder implements CellViewBuilderInterface
{
    public function __construct(
        private CellViewBuilderInterface $cellBuilder,
        private DataMapperInterface $dataMapper,
        private DataTransformerRegistryInterface $transformerRegistry,
    ) {
    }

    public function buildCell(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): CellView {
        $data = $this->dataMapper->getValue($property, $data);

        if ($property->getDataTransformer() !== null) {
            $dataTransformer = $this->transformerRegistry->get($property->getDataTransformer());
            $data = $dataTransformer->transform($property, $data);
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
