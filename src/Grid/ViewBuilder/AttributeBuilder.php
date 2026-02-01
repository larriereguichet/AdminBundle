<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;
use Twig\Runtime\EscaperRuntime;

final readonly class AttributeBuilder implements AttributeBuilderInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    /** @param array<string, string> $attributes */
    public function buildAttributes(array $attributes): ComponentAttributes
    {
        return new ComponentAttributes($attributes, $this->environment->getRuntime(EscaperRuntime::class));
    }
}
