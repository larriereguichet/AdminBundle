<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Grid\GridView;
use LAG\AdminBundle\Metadata\Operation;
use Twig\Environment;

class GridRenderer implements GridRendererInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function render(GridView $grid, Operation $operation): string
    {
        return $this->environment->render($grid->template, [
            'grid' => $grid,
            'operation' => $operation,
            'resource' => $operation->getResource(),
        ]);
    }
}
