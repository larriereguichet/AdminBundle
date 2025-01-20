<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

final readonly class PaginationProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): mixed
    {
        $data = $this->provider->provide($operation, $urlVariables, $context);

        if (!$operation instanceof CollectionOperationInterface || !$this->shouldPaginate($operation, $data)) {
            return $data;
        }

        if (!$operation->usePagination()) {
            return $data;
        }
        $pager = new Pagerfanta(new QueryAdapter($data, true, true));
        $pager->setMaxPerPage($operation->getItemsPerPage());
        $pager->setCurrentPage((int) ($context['page'] ?? 1));

        return $pager;
    }

    private function shouldPaginate(OperationInterface $operation, mixed $data): bool
    {
        if (!$operation instanceof CollectionOperationInterface) {
            return false;
        }

        if (!$data instanceof QueryBuilder && !$data instanceof Query) {
            return false;
        }

        return true;
    }
}
