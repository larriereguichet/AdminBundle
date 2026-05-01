<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\OperationInterface;

use function Symfony\Component\String\u;

final readonly class OperationFactory implements OperationFactoryInterface
{
    public function __construct(
        private ResourceFactoryInterface $resourceFactory,
    ) {
    }

    public function create(string $operationName): OperationInterface
    {
        $resourceName = u($operationName)->beforeLast('.')->toString();
        $operationName = u($operationName)->afterLast('.')->toString();

        return $this->resourceFactory->create($resourceName)->getOperation($operationName);
    }
}
