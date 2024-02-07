<?php

namespace LAG\AdminBundle\View\Component\Grid;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    template: 'components/card.html.twig',
)]
class CardGrid
{
    public int $columns = 4;
}
