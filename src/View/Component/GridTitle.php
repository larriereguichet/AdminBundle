<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:grid:title',
    template: '@LAGAdmin/components/grids/grid_title.html.twig',
)]
final class GridTitle
{
    public string $title;
    public ?string $translationDomain = null;
}
