<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Grid;

use LAG\AdminBundle\Grid\View\HeaderView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:grid_header',
    template: '@LAGAdmin/components/grids/header.html.twig',
    exposePublicProps: true,
)]
final class GridHeader
{
    public HeaderView $header;
}