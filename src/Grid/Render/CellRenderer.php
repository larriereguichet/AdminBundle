<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Render;

use LAG\AdminBundle\Grid\View\CellView;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;

final readonly class CellRenderer implements CellRendererInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function render(CellView $cell, array $options = []): string
    {
        if ($cell->template === null) {
            return '';
        }
        $attributes = array_merge($cell->attributes, $options['attributes'] ?? []);

        return $this->environment->render($cell->template, [
            'options' => $cell->options,
            'context' => $cell->context,
            'data' => $cell->data,
            'attributes' => new ComponentAttributes($attributes),
        ]);
    }
}
