<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;

interface GridViewBuilderInterface
{
    /**
     * Build a grid view for the given grid and operation.
     */
    public function build(
        string $gridName,
        CollectionOperationInterface $operation,
        mixed $data,
        array $context = [],
    ): GridView;
}
