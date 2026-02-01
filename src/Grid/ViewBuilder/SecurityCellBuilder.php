<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\Header;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;

/**
 * Check if the given property is allowed to be displayed. If the property is allowed, the property view build is
 * delegated to the next builder in the responsibility chain. If the property is not allowed, an empty view is returned.
 */
final readonly class SecurityCellBuilder implements CellBuilderInterface
{
    public function __construct(
        private CellBuilderInterface $cellBuilder,
        private PropertyPermissionCheckerInterface $permissionChecker,
        private AttributeBuilderInterface $attributeBuilder,
    ) {
    }

    public function buildHeader(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        array $context = [],
    ): Header {
        if (!$this->permissionChecker->isGranted($property)) {
            return new Header(
                name: $property->getName(),
                attributes: $this->attributeBuilder->buildAttributes($property->getHeaderAttributes()),
            );
        }

        return $this->cellBuilder->buildHeader($operation, $grid, $property, $context);
    }

    public function buildCell(
        OperationInterface $operation,
        GridInterface $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): Cell {
        if (!$this->permissionChecker->isGranted($property)) {
            return new Cell(
                name: $property->getName(),
                attributes: $this->attributeBuilder->buildAttributes($property->getAttributes()),
            );
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
