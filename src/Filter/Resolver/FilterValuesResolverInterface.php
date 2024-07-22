<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Resolver;

use LAG\AdminBundle\Filter\FilterInterface;

interface FilterValuesResolverInterface
{
    /**
     * Resolve filters data according to the given context provided by the current request.
     *
     * @param iterable<int|string, FilterInterface> $filters
     * @param array<string|mixed> $context
     *
     * @return array<string, mixed>
     */
    public function resolveFilters(iterable $filters, array $context): array;
}
