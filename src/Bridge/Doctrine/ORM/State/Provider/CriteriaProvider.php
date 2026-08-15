<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

final readonly class CriteriaProvider implements ProviderInterface
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

        $criteria = $context['criteria'] ?? [];

        if ($criteria === []) {
            return $data;
        }

        $rootAlias = $data->getRootAliases()[0];

        foreach ($criteria as $field => $value) {
            $paramName = 'criteria_'.$field;
            $data->andWhere($rootAlias.'.'.$field.' = :'.$paramName);
            $data->setParameter($paramName, $value);
        }

        return $data;
    }
}
