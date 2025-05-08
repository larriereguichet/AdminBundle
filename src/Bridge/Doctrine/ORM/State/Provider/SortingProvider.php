<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Exception\ORMException;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

use function Symfony\Component\String\u;

final readonly class SortingProvider implements ProviderInterface
{
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
        $orderBy = $operation->getOrderBy();
        $rootAlias = $data->getRootAliases()[0];

        if (($context['sort'] ?? false) && ($context['order'] ?? false)) {
            $orderBy[$context['sort']] = $context['order'];
        }

        foreach ($orderBy as $field => $direction) {
            $sort = $field;

            if ($resource->hasProperty($field)) {
                $property = $resource->getProperty($field);
                $sort = $property->getSortingPath();
            }

            if ($sort === null) {
                throw new ORMException(\sprintf(
                    'The property "%s" can not be sorted by as the sorting path is null.',
                    $field,
                ));
            }
            $order = u($sort);

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
