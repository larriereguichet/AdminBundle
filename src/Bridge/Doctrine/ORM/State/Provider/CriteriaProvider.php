<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use function Symfony\Component\String\u;

final readonly class CriteriaProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $uriVariables, $context);

        if (!$data instanceof QueryBuilder) {
            return $data;
        }

        if (!$operation instanceof CollectionOperationInterface || $operation->getCriteria() === []) {
            return $data;
        }

        foreach ($operation->getCriteria() as $criteria => $value) {
            $alias = $data->getRootAliases()[0];
            $dql = u('entity.field = :'.$criteria.'_value')
                ->replace('entity', $alias)
                ->replace('field', $criteria)
            ;
            $data->andWhere($dql->toString())
                ->setParameter($criteria.'_value', $value)
            ;
        }

        return $data;
    }
}