<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

final readonly class FilterProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private FilterApplicatorInterface $filterApplicator,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if (!$data instanceof QueryBuilder || !$operation instanceof CollectionOperationInterface) {
            return $data;
        }

        foreach ($context['filters'] ?? [] as $name => $value) {
            // Allow other providers to use custom filters even if not defined in the operation
            if (!$operation->hasFilter($name)) {
                continue;
            }
            $filter = $operation->getFilter($name);

            if ($this->filterApplicator->supports($operation, $filter, $data, $value)) {
                $this->filterApplicator->apply($operation, $filter, $data, $value);
            }
        }

        return $data;
    }
}
