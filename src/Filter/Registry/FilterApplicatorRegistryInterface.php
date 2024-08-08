<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Registry;

use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;

interface FilterApplicatorRegistryInterface
{
    public function get(string $name): FilterApplicatorInterface;

    public function has(string $name): bool;
}
