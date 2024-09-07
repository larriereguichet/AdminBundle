<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Render;

use LAG\AdminBundle\Grid\View\HeaderView;

interface HeaderRendererInterface
{
    public function render(HeaderView $header, array $options = []): string;
}
