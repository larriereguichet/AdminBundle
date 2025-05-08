<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Pagerfanta\Adapter\CallbackAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;

// TODO fix output generation
final readonly class OutputProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if ($operation->getOutput() === null) {
            return $data;
        }

        if ($data instanceof PagerfantaInterface) {
            return new Pagerfanta(new CallbackAdapter(fn () => $data->getNbResults(), fn () => $data->getCurrentPageResults()));
        }

        return $data;
    }
}
