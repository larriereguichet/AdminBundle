<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Grid;

use LAG\AdminBundle\Grid\View\CellView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:grid_cell',
    template: '@LAGAdmin/components/grids/cell.html.twig',
    exposePublicProps: true,
)]
final class GridCell
{
    public CellView $cell;
}
