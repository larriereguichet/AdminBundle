<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;

interface PropertyFactoryInterface
{
    /**
     * @return PropertyInterface[]
     */
    public function createCollection(OperationInterface $operation): array;
}
