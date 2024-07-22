<?php

namespace LAG\AdminBundle\View\Component\Cell;

use LAG\AdminBundle\Grid\View\CellView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:image',
    template: '@LAGAdmin/components/cells/image.html.twig',
)]
final class ImageComponent
{
    public string $src;
    public string $alt;

    public function mount(
        mixed $data,
        CellView $cell,
    ): void {
        $this->src = $data;
        $this->alt = $cell->label ?? $this->src;
    }
}
