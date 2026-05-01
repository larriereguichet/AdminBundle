<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;

final readonly class ConditionCellBuilder implements CellBuilderInterface
{
    public function __construct(
        private ConditionMatcherInterface $conditionMatcher,
        private CellBuilderInterface $cellBuilder,
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    public function buildCell(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): CellView {
        if ($property->getCondition() !== null && !$this->conditionMatcher->matchCondition($property, $data, $context)) {
            return new CellView(
                name: $property->getName(),
                attributes: $this->attributeBuilder->buildAttributes($property->getAttributes()),
            );
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
