<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Results;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Exception\Exception;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ResultsHandler implements ResultsHandlerInterface
{
    public function handle($data, bool $pagination, int $page = 1, int $maxPerPage = 25)
    {
        if ($pagination) {
            $adapter = $this->getAdapter($data);

            $pager = new Pagerfanta($adapter);
            $pager->setCurrentPage($page);
            $pager->setMaxPerPage($maxPerPage);

            return $pager->getCurrentPageResults();
        }

        if ($data instanceof Query) {
            return $data->getResult();
        }

        if ($data instanceof QueryBuilder) {
            return $data->getQuery()->getResult();
        }

        return $data;
    }

    private function getAdapter($data): AdapterInterface
    {
        if ($data instanceof QueryBuilder || $data instanceof Query) {
            return new DoctrineORMAdapter($data, true, true);
        }

        if (is_array($data)) {
            return new ArrayAdapter($data);
        }

        if (is_iterable($data)) {
            return new ArrayAdapter(iterator_to_array($data));
        }

        if ($data instanceof Collection) {
            return new DoctrineCollectionAdapter($data);
        }

        throw new Exception('Unable to find an adapter for type "'.gettype($data).'"');
    }
}
