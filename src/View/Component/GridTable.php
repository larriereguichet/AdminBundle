<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:grid:table',
    template: '@LAGAdmin/components/grids/grid_table.html.twig',
)]
final class GridTable
{
    public bool $displayHeaders = true;
}
