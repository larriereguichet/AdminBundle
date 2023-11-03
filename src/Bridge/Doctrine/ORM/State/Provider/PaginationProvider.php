<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class PaginationProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $uriVariables, $context);

        if (
            !$operation instanceof CollectionOperationInterface ||
            !$operation->hasPagination() ||
            (!$data instanceof QueryBuilder && !$data instanceof Query)
        ) {
            return $data;
        }
        $pager = new Pagerfanta(new QueryAdapter($data, true, true));
        $pager->setMaxPerPage($operation->getItemPerPage());
        $pager->setCurrentPage($context['page'] ?? 1);

        return $pager;
    }
}
