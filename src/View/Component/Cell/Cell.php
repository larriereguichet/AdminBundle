<?php

namespace LAG\AdminBundle\View\Component\Cell;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:grid:cell',
    template: '@LAGAdmin/components/grids/cells/cell.html.twig',
)]
class Cell
{
    public ?string $template = null;
    public ?string $component = null;
    public array $context = [];

    public function mount(\LAG\AdminBundle\Grid\View\Cell $cell): void
    {
        dump($cell);
        $this->template = $cell->template;
        $this->component = $cell->component;
        $this->context = [
            'data' => $cell->data,
        ];
    }
}
