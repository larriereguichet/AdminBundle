<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Render;

use LAG\AdminBundle\Metadata\Action;

interface ActionRendererInterface
{
    /**
     * Render a action link or button according to its Twig template. Data can be provided to generate urls. An array of
     * options can be passed to the template.
     *
     * @param array<string, mixed> $options
     */
    public function render(Action $action, mixed $data = null, array $options = []): string;
}
