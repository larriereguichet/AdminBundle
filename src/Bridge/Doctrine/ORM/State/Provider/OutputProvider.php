<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
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

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $uriVariables, $context);

        if ($operation->getOutput() === null) {
            return $data;
        }

        if ($data instanceof PagerfantaInterface) {
            return new Pagerfanta(new CallbackAdapter(function () use ($data) {
                return $data->getNbResults();
            }, function () use ($data) {
                return $data->getCurrentPageResults();
            }));
        }

        return $data;
    }
}
