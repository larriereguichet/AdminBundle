<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Applicator;

use LAG\AdminBundle\Metadata\FilterInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class CompositeFilterApplicator implements FilterApplicatorInterface
{
    public function __construct(
        /** @var iterable<FilterApplicatorInterface> $applicators */
        private iterable $applicators,
    ) {
    }

    public function supports(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): bool
    {
        foreach ($this->applicators as $applicator) {
            if ($applicator->supports($operation, $filter, $data, $filterValue)) {
                return true;
            }
        }

        return false;
    }

    public function apply(OperationInterface $operation, FilterInterface $filter, mixed $data, mixed $filterValue): void
    {
        foreach ($this->applicators as $applicator) {
            if ($applicator->supports($operation, $filter, $data, $filterValue)) {
                $applicator->apply($operation, $filter, $data, $filterValue);
            }
        }
    }
}
