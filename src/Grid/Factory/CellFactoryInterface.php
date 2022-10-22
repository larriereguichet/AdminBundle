<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Grid\Factory;

use LAG\AdminBundle\Grid\Cell;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;

interface CellFactoryInterface
{
    public function create(PropertyInterface $property, mixed $data): Cell;
}
