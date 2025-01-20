<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

final readonly class ResultProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if ($data instanceof QueryBuilder) {
            $data = $data->getQuery();
        }

        if ($data instanceof Query) {
            if ($operation instanceof CollectionOperationInterface) {
                return $data->getResult();
            }

            return $data->getOneOrNullResult();
        }

        return $data;
    }
}
