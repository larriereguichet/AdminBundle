<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

final readonly class IdentifierProvider implements ProviderInterface
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
        $index = 0;
        $rootAlias = $data->getRootAliases()[0];

        foreach ($operation->getIdentifiers() as $identifier) {
            if ($uriVariables[$identifier] ?? false) {
                $parameterName = 'identifier_'.$index;
                $data
                    ->andWhere(\sprintf($rootAlias.'.%s = :%s', $identifier, $parameterName))
                    ->setParameter($parameterName, $uriVariables[$identifier])
                ;
                ++$index;
            }
        }

        return $data;
    }
}
