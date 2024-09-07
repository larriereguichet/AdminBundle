<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Render;

use LAG\AdminBundle\Grid\View\HeaderView;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;

final readonly class HeaderRenderer implements HeaderRendererInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    public function render(HeaderView $header, array $options = []): string
    {
        if ($header->template === null) {
            return '';
        }
        $attributes = array_merge($options['attributes'] ?? [], $header->attributes->all());

        return $this->environment->render($header->template, [
            'header' => $header,
            'options' => $options,
            'attributes' => new ComponentAttributes($attributes),
        ]);
    }
}
