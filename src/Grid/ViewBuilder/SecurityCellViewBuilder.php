<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;

/**
 * Check if the given property is allowed to be displayed. If the property is allowed, the property view build is
 * delegated to the next builder in the responsibility chain. If the property is not allowed, an empty view is returned.
 */
final readonly class SecurityCellViewBuilder implements CellViewBuilderInterface
{
    public function __construct(
        private CellViewBuilderInterface $cellBuilder,
        private PropertyPermissionCheckerInterface $permissionChecker,
    ) {
    }

    public function buildCell(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        mixed $data,
        array $context = []
    ): CellView {
        if (!$this->permissionChecker->isGranted($property)) {
            return new CellView(name: $property->getName());
        }

        return $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);
    }
}
