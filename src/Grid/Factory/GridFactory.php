<?php

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Admin\Admin;
use LAG\AdminBundle\Event\GridFieldEvent;
use LAG\AdminBundle\Grid\Grid;
use LAG\AdminBundle\Grid\Header;
use LAG\AdminBundle\Grid\Row;
use LAG\AdminBundle\Metadata\Action;
use Pagerfanta\Pagerfanta;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GridFactory implements GridFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function create(mixed $data, Admin $admin, Action $action): Grid
    {
        $headers = [];
        $rows = [];
        $gridName = $admin->getName().'_'.$action->getName();

        foreach ($action->getFields() as $field) {
            $headers[] = new Header(
                $field->getLabel(),
                $field->isSortable(),
            );
        }

        if ($data instanceof Pagerfanta) {
            foreach ($data->getCurrentPageResults() as $index => $rowData) {
                $rows[] = new Row($index, $rowData);
            }
        }
        $this->eventDispatcher->dispatch(new GridFieldEvent($gridName, $rows));

        return new Grid($gridName, $headers, $rows);
    }
}
