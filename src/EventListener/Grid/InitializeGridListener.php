<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Grid;

use LAG\AdminBundle\Event\GridEvent;
use function Symfony\Component\String\u;

class InitializeGridListener
{
    public function __invoke(GridEvent $event): void
    {
        $grid = $event->getGrid();
        $operation = $event->getOperation();

        if ($grid->getType() === null) {
            $grid = $grid->withType('table');
        }

        if ($grid->getTemplate() === null) {
            $template = match ($grid->getType()) {
                'card' => '@LAGAdmin/grids/card/card_grid.html.twig',
                default => '@LAGAdmin/grids/table/table_grid.html.twig',
            };
            $grid = $grid->withTemplate($template);
        }

        if ($grid->getName() === null) {
            $gridName = u('{resource}.{operation}.grid')
                ->replace('{resource}', $operation->getResource()->getName())
                ->replace('{operation}', $operation->getName())
            ;
            $grid = $grid->withName($gridName->toString());
        }

        $event->setGrid($grid);
    }
}
