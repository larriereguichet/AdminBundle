<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\Operation;
use Twig\Environment;

final readonly class GridRenderer implements GridRendererInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function render(GridView $grid, Operation $operation, array $options = []): string
    {
        return $this->environment->render($grid->template, [
            'grid' => $grid,
            'options' => array_merge_recursive($grid->options, $options),
            'operation' => $operation,
            'resource' => $operation->getResource(),
        ]);
    }
}
