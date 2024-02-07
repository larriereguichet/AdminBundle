<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Builder;

use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use Symfony\Component\Form\FormView;

interface CellBuilderInterface
{
    public function build(
        PropertyInterface $property,
        mixed $data,
        array $attributes = []
    ): Cell;
}
