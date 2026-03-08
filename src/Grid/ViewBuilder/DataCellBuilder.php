<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;

final readonly class DataCellBuilder implements CellBuilderInterface
{
    public function __construct(
        private CellBuilderInterface $cellBuilder,
        private DataMapperInterface $dataMapper,
        private DataTransformerRegistryInterface $transformerRegistry,
    ) {
    }

    public function buildCell(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): Cell {
        $data = $this->dataMapper->getPropertyValue($property, $data);

        if ($property->getDataTransformer() !== null) {
            $dataTransformer = $this->transformerRegistry->get($property->getDataTransformer());
            $data = $dataTransformer->transform($property, $data);
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
