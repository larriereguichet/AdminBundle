<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\Grid\Cell;
use LAG\AdminBundle\Grid\GridView;
use LAG\AdminBundle\Grid\View\CellRendererInterface;
use LAG\AdminBundle\Grid\View\GridRendererInterface;
use LAG\AdminBundle\Metadata\Operation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GridExtension extends AbstractExtension
{
    public function __construct(
        private GridRendererInterface $gridRenderer,
        private CellRendererInterface $cellRenderer,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_grid', [$this, 'renderGrid'], ['is_safe' => ['html']]),
            new TwigFunction('lag_admin_grid_cell', [$this, 'renderCell'], ['is_safe' => ['html']]),
        ];
    }

    public function renderGrid(GridView $grid, Operation $operation): string
    {
        return $this->gridRenderer->render($grid, $operation);
    }

    public function renderCell(Cell $cell): string
    {
        return $this->cellRenderer->render($cell);
    }
}
