<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Resource\Metadata\Link;

interface LinkRendererInterface
{
    /**
     * Render an action link or button according to its Twig template. Data can be provided to generate urls. An array
     * of options can be passed to the template.
     *
     * @param array<string, mixed> $options
     */
    public function render(Link $link, mixed $data = null, array $options = []): string;
}
