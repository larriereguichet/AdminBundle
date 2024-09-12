<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

use function Symfony\Component\String\u;

final readonly class SortingProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $uriVariables, $context);

        if (!$data instanceof QueryBuilder || !$operation instanceof CollectionOperationInterface) {
            return $data;
        }
        $resource = $operation->getResource();
        $orderBy = $operation->getOrderBy();
        $rootAlias = $data->getRootAliases()[0];

        if (($context['sort'] ?? false) && ($context['order'] ?? false)) {
            $orderBy[$context['sort']] = $context['order'];
        }

        foreach ($orderBy as $field => $direction) {
            $property = $resource->getProperty($field);
            $order = u($property->getPropertyPath() ?? $field);

            if ($order->containsAny('.')) {
                $alias = $rootAlias;

                foreach ($order->beforeLast('.')->split('.') as $join) {
                    $newAlias = $join.'_entity';
                    $data->leftJoin($alias.'.'.$join, $newAlias);
                    $alias = $newAlias;
                }
                $sort = $order->afterLast('.')->toString();
                $data->addOrderBy($alias.'.'.$sort, $direction);
            } else {
                $data->addOrderBy($order->prepend($rootAlias, '.')->toString(), $direction);
            }
        }

        return $data;
    }
}
