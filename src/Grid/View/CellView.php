<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Resource\Metadata\PropertyInterface;

final readonly class CellView
{
    public function __construct(
        public string $name,
        public ?PropertyInterface $options = null,
        public ?string $template = null,
        public ?string $label = null,
        public mixed $data = null,
        public array $attributes = [],
        public array $containerAttributes = [],
        public array $context = [],
    ) {
    }
}
