<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Applicator;

use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

interface FilterApplicatorInterface
{
    public const string SERVICE_TAG = 'lag_admin.filter_applicator';

    public function supports(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): bool;

    public function apply(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): void;
}
