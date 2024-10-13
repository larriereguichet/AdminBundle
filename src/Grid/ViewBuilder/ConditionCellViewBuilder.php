<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class ConditionCellViewBuilder implements CellViewBuilderInterface
{
    public function __construct(
        private ConditionMatcherInterface $conditionMatcher,
        private CellViewBuilderInterface $cellBuilder,
    ) {
    }

    public function buildCell(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): CellView {
        if ($property->getCondition() !== null && !$this->conditionMatcher->matchCondition($property, $data, $context)) {
            return new CellView(name: $property->getName());
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
