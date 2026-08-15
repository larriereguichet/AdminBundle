<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Metadata\PropertyInterface;
use Symfony\UX\TwigComponent\ComponentAttributes;

readonly class CellView
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(
        public string $name,
        public ComponentAttributes $attributes,
        public ?PropertyInterface $property = null,
        public ?string $template = null,
        public ?string $component = null,
        public ?string $label = null,
        public mixed $data = null,
        public array $context = [],
    ) {
    }
}
