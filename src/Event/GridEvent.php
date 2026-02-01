<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GridEvent extends Event implements ResourceEventInterface
{
    public function __construct(
        private Grid $grid,
        private readonly OperationInterface $operation
    ) {
    }

    public function getGrid(): Grid
    {
        return $this->grid;
    }

    public function setGrid(Grid $grid): void
    {
        $this->grid = $grid;
    }

    public function getOperation(): OperationInterface
    {
        return $this->operation;
    }

    public function getResource(): Resource
    {
        return $this->operation->getResource();
    }
}
