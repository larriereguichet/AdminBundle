<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Metadata\Attribute\Action;
use LAG\AdminBundle\Grid\View\Cell;

interface ActionBuilderInterface
{
    /**
     * @param Action $action
     * @param mixed $data
     * @param array<string|mixed> $context
     * @return Cell|null
     */
    public function buildAction(Action $action, mixed $data, array $context = []): ?Cell;
}
