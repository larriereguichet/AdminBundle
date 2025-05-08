<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;

final readonly class SecurityHeaderViewBuilder implements HeaderViewBuilderInterface
{
    public function __construct(
        private HeaderViewBuilderInterface $headerBuilder,
        private PropertyPermissionCheckerInterface $permissionChecker,
    ) {
    }

    public function buildHeader(
        OperationInterface $operation,
        Grid $grid,
        PropertyInterface $property,
        array $context = []
    ): HeaderView {
        if (!$this->permissionChecker->isGranted($property)) {
            return new HeaderView(name: $property->getName());
        }

        return $this->headerBuilder->buildHeader($operation, $grid, $property, $context);
    }
}
