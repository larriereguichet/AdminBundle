<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View\Builder;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Grid\View\Cell;

final readonly class ConditionCellBuilder implements CellBuilderInterface
{
    public function __construct(
        private ConditionMatcherInterface $conditionMatcher,
        private CellBuilderInterface $cellBuilder,
    ) {
    }

    public function buildCell(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): Cell {
        if ($property->getCondition() !== null && !$this->conditionMatcher->matchCondition($property, $data, $context)) {
            // TODO use empty cell ?
            return new Cell(name: $property->getName());
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
