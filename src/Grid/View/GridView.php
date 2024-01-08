<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use Symfony\Component\Form\FormView;

class GridView
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $template,
        public readonly Row $headers,
        public readonly iterable $rows,
        public readonly iterable $attributes = [],
        public readonly array $context = [],
        public array $options = [],
        public ?FormView $form = null,
    ) {
    }
}
