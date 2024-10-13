<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Exception\ManagerNotFoundException;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;

final readonly class ORMProvider implements ProviderInterface
{
    public function __construct(
        private Registry $registry,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Create) {
            $class = $operation->getResource()->getDataClass();

            return new $class();
        }
        $manager = $this->registry->getManagerForClass($operation->getResource()->getDataClass());

        if ($manager === null) {
            throw new ManagerNotFoundException($operation);
        }
        /** @var EntityRepository $repository */
        $repository = $manager->getRepository($operation->getResource()->getDataClass());
        // Add a suffix to avoid error if the resource is named with a reserved keyword (like group)
        $rootAlias = $operation->getResource()->getName().'_entity';
        $queryBuilder = $repository->createQueryBuilder($rootAlias);
        $index = 0;

        foreach ($operation->getIdentifiers() as $identifier) {
            if ($uriVariables[$identifier] ?? false) {
                $parameterName = 'identifier_'.$index;
                $queryBuilder->andWhere(\sprintf($rootAlias.'.%s = :%s', $identifier, $parameterName))
                    ->setParameter($parameterName, $uriVariables[$identifier])
                ;
                ++$index;
            }
        }

        return $queryBuilder;
    }
}
