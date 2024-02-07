<?php

namespace LAG\AdminBundle\View\Component\Grid;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:grid:table_header',
    template: '@LAGAdmin/components/grids/table_header.html.twig'
)]
class TableHeader
{
    public bool $sortable = false;
    public string $text;
    public ?string $translationDomain = null;
}
