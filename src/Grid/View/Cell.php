<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\View;

use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Form\FormView;

readonly class Cell
{
    public function __construct(
        public string $name,
        public ?string $template = null,
        public ?string $component = null,
        public mixed $data = null,
        public array $attributes = [],
        public ?PropertyInterface $property = null,
        public ?FormView $form = null,
        public array $children = [],
    ) {
    }
}
