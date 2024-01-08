<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Render;

use LAG\AdminBundle\Grid\View\Cell;
use Twig\Environment;

readonly class CellRenderer implements CellRendererInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function render(Cell $cell, array $options = []): string
    {
        return $this->environment->render($cell->template, [
            'options' => $cell->property,
            'data' => $cell->data,
            'attributes' => array_merge($options['attributes'] ?? [], $cell->attributes),
            'form' => $cell->form,
            'children' => $cell->children,
            'cell' => $cell,
        ]);
    }
}
