<?php

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Event\GridEvent;
use LAG\AdminBundle\Grid\Grid;
use LAG\AdminBundle\Grid\Header;
use LAG\AdminBundle\Grid\Row;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;

class GridFactory implements GridFactoryInterface
{
    public function __construct(
        private CellFactoryInterface $cellFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(CollectionOperationInterface $operation, iterable $data): Grid
    {
        $headers = [];
        $rows = [];

        foreach ($operation->getProperties() as $property) {
            $headers[] = new Header(
                $property->getName(),
                $property->getLabel() ?? u($property->getName())->title()->toString(),
                $property->isSortable(),
            );
        }

        foreach ($data as $index => $item) {
            $fields = [];

            foreach ($operation->getProperties() as $property) {
                $fields[$property->getName()] = $this->cellFactory->create($property, $item);
            }
            $rows[] = new Row($index, $fields, $item);
        }
        $grid = new Grid(
            $operation->getResourceName().'_'.$operation->getName(),
            $operation->getGridTemplate(),
            $headers,
            $rows
        );
        $this->eventDispatcher->dispatch(new GridEvent($grid), GridEvent::GRID_CREATED);

        return $grid;
    }
}
