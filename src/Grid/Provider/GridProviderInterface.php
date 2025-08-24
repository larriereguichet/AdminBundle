<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Provider;

use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\OperationInterface;

/**
 * Build a single grid to be used in one or several resource collection view.
 */
interface GridProviderInterface
{
    public function supports(OperationInterface $operation): bool;

    public function getGrid(OperationInterface $operation): Grid;
}
