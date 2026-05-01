<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Runtime;

use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Runtime\EscaperRuntime;

final readonly class AttributeRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private Environment $environment,
    ) {
    }

    /** @param array<string, string|bool> $attributes */
    public function createAttributes(array $attributes): ComponentAttributes
    {
        return new ComponentAttributes(
            attributes: $attributes,
            escaper: $this->environment->getRuntime(EscaperRuntime::class),
        );
    }
}
