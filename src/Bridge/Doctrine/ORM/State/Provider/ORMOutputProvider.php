<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Pagerfanta\Adapter\CallbackAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;

class ORMOutputProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $uriVariables, $context);

        if ($operation->getOutputClass() === null) {
            return $data;
        }

        if ($data instanceof PagerfantaInterface) {
            $pager = new Pagerfanta(new CallbackAdapter(function () use ($data) {
                return $data->getNbResults();
            }, function () {

            }));
        }

        return $data;
    }
}
