<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Component;

use LAG\AdminBundle\Grid\View\HeaderView;

final class GridHeader
{
    public HeaderView $header;

    public function mount(HeaderView $header): void
    {
        $this->header = $header;
    }
}
