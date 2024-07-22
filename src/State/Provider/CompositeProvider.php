<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;

readonly class CompositeProvider implements ProviderInterface
{
    public function __construct(
        /** @var ProviderInterface[] $providers */
        private iterable $providers = [],
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var ProviderInterface $provider */
        foreach ($this->providers as $provider) {
            if ($provider::class === $operation->getProvider()) {
                return $provider->provide($operation, $uriVariables, $context);
            }
        }

        throw new Exception(sprintf(
            'The admin resource "%s" and operation "%s" is not supported by any provider',
            $operation->getResource()->getName(),
            $operation->getName()
        ));
    }
}
