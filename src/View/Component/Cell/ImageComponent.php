<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component\Cell;

use LAG\AdminBundle\Grid\View\Cell;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'lag_admin:image',
    template: '@LAGAdmin/components/cells/image.html.twig',
)]
/** @param array<string, mixed> $data */
final class ImageComponent
{
    public string $src;
    public string $alt;

    public function mount(
        mixed $data,
        Cell $cell,
    ): void {
        $this->src = $data;
        $this->alt = $cell->label ?? $this->src;
    }
}
