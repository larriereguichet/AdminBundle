<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Metadata\Action;

interface ActionViewBuilderInterface
{
    public function buildActions(Action $action, mixed $data, array $context = []): ?CellView;
}
