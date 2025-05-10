<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\OperationInterface;

final readonly class CompositeProvider implements ProviderInterface
{
    public function __construct(
        /** @var ProviderInterface[] $providers */
        private iterable $providers = [],
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        /** @var ProviderInterface $provider */
        foreach ($this->providers as $provider) {
            if ($provider::class === $operation->getProvider()) {
                return $provider->provide($operation, $urlVariables, $context);
            }
        }

        throw new Exception(\sprintf(
            'The resource "%s" and operation "%s" in the application "%s" is not supported by any provider',
            $operation->getResource()->getName(),
            $operation->getName(),
            $operation->getResource()->getApplication(),
        ));
    }
}
