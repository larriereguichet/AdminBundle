<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Grid;

use LAG\AdminBundle\Grid\View\CellView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\ComponentAttributes;

#[AsTwigComponent(
    name: 'lag_admin:grid_cell',
    template: '@LAGAdmin/components/grids/cell.html.twig',
    exposePublicProps: true,
)]
final class GridCell
{
    public CellView $cell;
    public ComponentAttributes $attributes;
    public ComponentAttributes $rowAttributes;

    public function mount(
        CellView $cell,
        array $rowAttributes = [],
    ): void {
        $this->cell = $cell;
        $this->rowAttributes = new ComponentAttributes($cell->rowAttributes + $rowAttributes);
    }
}
