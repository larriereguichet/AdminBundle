<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Grid\Render\CellRendererInterface;
use LAG\AdminBundle\Grid\Render\GridRendererInterface;
use LAG\AdminBundle\Grid\Render\HeaderRendererInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Grid\View\HeaderView;
use LAG\AdminBundle\Resource\Metadata\Operation;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class GridHelper implements GridHelperInterface, RuntimeExtensionInterface
{
    public function __construct(
        private GridRendererInterface $gridRenderer,
        private HeaderRendererInterface $headerRenderer,
        private CellRendererInterface $cellRenderer,
    ) {
    }

    public function renderGrid(GridView $grid, Operation $operation): string
    {
        return $this->gridRenderer->render($grid, $operation);
    }

    public function renderHeader(HeaderView $header, array $options = []): string
    {
        return $this->headerRenderer->render($header, $options);
    }

    public function renderCell(CellView $cell, array $options = []): string
    {
        return $this->cellRenderer->render($cell, $options);
    }
}
