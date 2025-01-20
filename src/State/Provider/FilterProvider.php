<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Filter\Resolver\FilterValuesResolverInterface;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

final readonly class FilterProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private FilterValuesResolverInterface $filterValuesResolver,
        private FilterApplicatorInterface $filterApplicator,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if (!$data instanceof QueryBuilder || !$operation instanceof CollectionOperationInterface) {
            return $data;
        }
        $filterValues = $this->filterValuesResolver->resolveFilters($operation->getFilters(), $context);

        foreach ($filterValues as $filterName => $filterValue) {
            // Allow other providers to use custom filters even if not defined in the operation
            if (!$operation->hasFilter($filterName)) {
                continue;
            }
            $filter = $operation->getFilter($filterName);

            if ($this->filterApplicator->supports($operation, $filter, $data, $filterValue)) {
                $this->filterApplicator->apply($operation, $filter, $data, $filterValue);
            }
        }

        return $data;
    }
}
