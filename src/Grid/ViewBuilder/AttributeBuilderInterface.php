<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\ViewBuilder;

use Symfony\UX\TwigComponent\ComponentAttributes;

/**
 * Build attributes array into ComponentAttributes object to help view renders.
 */
interface AttributeBuilderInterface
{
    /** @param array<string, string> $attributes */
    public function buildAttributes(array $attributes): ComponentAttributes;
}
