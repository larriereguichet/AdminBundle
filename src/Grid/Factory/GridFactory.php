<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Event\GridEvent;
use LAG\AdminBundle\Grid\Grid;
use LAG\AdminBundle\Grid\Header;
use LAG\AdminBundle\Grid\Row;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
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
        $resource = $operation->getResource();

        foreach ($operation->getProperties() as $property) {
            $label = $property->getLabel();

            if (!$label && $resource->getTranslationPattern()) {
                $label = u($operation->getResource()->getTranslationPattern())
                    ->replace('resource', $resource->getName())
                    ->replace('operation', $operation->getName())
                    ->append('.', $property->getName())
                    ->lower()
                    ->toString()
                ;
            }

            if (!$label) {
                $label = u($property->getName())->title()->toString();
            }

            $headers[] = new Header(
                $property->getName(),
                $label,
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
            $operation->getResource()->getName().'_'.$operation->getName(),
            $operation->getGridTemplate(),
            $headers,
            $rows
        );
        $this->eventDispatcher->dispatch(new GridEvent($grid), GridEvent::GRID_CREATED);

        return $grid;
    }
}
