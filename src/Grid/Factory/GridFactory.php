<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Event\GridEvent;
use LAG\AdminBundle\Grid\GridView;
use LAG\AdminBundle\Grid\Header;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\Row;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GridFactory implements GridFactoryInterface
{
    public function __construct(
        private GridRegistryInterface $registry,
        private CellFactoryInterface $cellFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(CollectionOperationInterface $operation, iterable $data): GridView
    {
        $headers = [];
        $rows = [];
        $grid = $this->registry->get($operation->getGrid());

        foreach ($operation->getProperties() as $property) {
            $headers[] = new Header(
                name: $property->getName(),
                label: $property->getLabel(),
                sortable: $property->isSortable(),
                translationDomain: $operation->getResource()->getTranslationDomain(),
            );
        }

        foreach ($data as $index => $item) {
            $fields = [];

            foreach ($operation->getProperties() as $property) {
                if (\array_key_exists($property::class, $grid->getTemplateMapping())) {
                    $property = $property->withTemplate($grid->getTemplateMapping()[$property::class]);
                }
                $fields[$property->getName()] = $this->cellFactory->create($property, $item);
            }
            $rows[] = new Row($index, $fields, $item);
        }
        $grid = new GridView(
            $operation->getResource()->getName().'_'.$operation->getName().'_'.$grid->getName(),
            $grid->getTemplate(),
            $headers,
            $rows
        );
        $this->eventDispatcher->dispatch(new GridEvent($grid), GridEvent::GRID_CREATED);

        return $grid;
    }
}
