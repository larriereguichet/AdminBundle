<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Grid\Cell;
use Twig\Environment;

class CellRenderer implements CellRendererInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function render(Cell $cell): string
    {
        return $this->environment->render($cell->template, $cell->context);
    }
}
