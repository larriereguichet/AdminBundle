<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\QuillJs\Render;

interface QuillJsRendererInterface
{
    public function render(string|array $json): string;
}
