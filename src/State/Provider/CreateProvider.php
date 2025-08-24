<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class CreateProvider implements ProviderInterface
{
    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $class = $operation->getResource()->getResourceClass();

        return new $class();
    }
}
