<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Provider;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

/**
 * Build a single grid to be used in one or several resource collection view.
 */
interface GridProviderInterface
{
    public function supports(string $gridName): bool;

    public function provide(string $gridName): GridInterface;
}
