<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

use function Symfony\Component\String\u;

final readonly class SortingProvider implements ProviderInterface
{
    private const array DIRECTIONS = ['ASC', 'DESC'];

    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if (!$data instanceof QueryBuilder || !$operation instanceof CollectionOperationInterface) {
            return $data;
        }
        $resource = $operation->getResource();
        $rootAlias = $data->getRootAliases()[0];
        $orderBy = $this->resolveOrderBy($operation, $context);
        $aliases = $data->getAllAliases();

        foreach ($orderBy as $sort => $direction) {
            $direction = strtoupper((string) $direction);

            if (!\in_array($direction, self::DIRECTIONS, true)) {
                continue;
            }

            if ($resource->hasProperty($sort)) {
                $property = $resource->getProperty($sort);

                if (!$property->isSortable()) {
                    continue;
                }

                if ($property->getSortingPath() !== null) {
                    $sort = $property->getSortingPath();
                }
            }

            $order = u((string) $sort);

            if (!$order->containsAny('.')) {
                $data->addOrderBy($order->prepend($rootAlias, '.')->toString(), $direction);

                continue;
            }
            $alias = $rootAlias;

            foreach ($order->beforeLast('.')->split('.') as $join) {
                $newAlias = $join.'_entity';

                // Another filter or another sorting path may already have joined that relation on the same query
                if (!\in_array($newAlias, $aliases, true)) {
                    $data->leftJoin($alias.'.'.$join, $newAlias);
                    $aliases[] = $newAlias;
                }
                $alias = $newAlias;
            }
            $data->addOrderBy($alias.'.'.$order->afterLast('.')->toString(), $direction);
        }

        return $data;
    }

    /**
     * The operation defines the default ordering of the collection. It is overridden, entry by entry, by the
     * "order_by" context key, and then by the sort and order query parameters.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    private function resolveOrderBy(CollectionOperationInterface $operation, array $context): array
    {
        $orderBy = $operation->getOrderBy();

        if (\is_array($context['order_by'] ?? null)) {
            $orderBy = array_merge($orderBy, $context['order_by']);
        }
        $sort = $context['sort'] ?? null;
        $order = $context['order'] ?? null;

        // The sort and order parameters come from the query string: only a declared sortable property is accepted,
        // as the value ends up in the DQL order by clause
        if (\is_string($sort) && \is_string($order) && $operation->getResource()->hasProperty($sort)) {
            $orderBy[$sort] = $order;
        }

        return $orderBy;
    }
}
