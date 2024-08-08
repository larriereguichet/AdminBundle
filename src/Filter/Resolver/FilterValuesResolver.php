<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Filter\Resolver;

final readonly class FilterValuesResolver implements FilterValuesResolverInterface
{
    public function resolveFilters(iterable $filters, array $context): array
    {
        $filteredData = [];

        foreach ($filters as $filter) {
            $data = $context['filters'][$filter->getName()] ?? null;

            if ($data !== null) {
                $filteredData[$filter->getName()] = $data;
            }
        }

        return $filteredData;
    }
}
