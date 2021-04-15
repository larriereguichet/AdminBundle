<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Results;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Exception\Exception;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Doctrine\Collections\CollectionAdapter;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

class ResultsHandler implements ResultsHandlerInterface
{
    public function handle($data, bool $pagination, int $page = 1, int $maxPerPage = 25): object
    {
        if ($pagination) {
            $adapter = $this->getAdapter($data);

            $pager = new Pagerfanta($adapter);
            $pager->setCurrentPage($page);
            $pager->setMaxPerPage($maxPerPage);

            $data = $pager;
        }

        if ($data instanceof Query) {
            $data = $data->getResult();
        }

        if ($data instanceof QueryBuilder) {
            $data = $data->getQuery()->getResult();
        }

        if (\is_array($data)) {
            $data = new ArrayCollection($data);
        }

        return $data;
    }

    private function getAdapter($data): AdapterInterface
    {
        if ($data instanceof QueryBuilder || $data instanceof Query) {
            return new QueryAdapter($data, true, true);
        }

        if ($data instanceof Collection) {
            return new CollectionAdapter($data);
        }

        if (\is_array($data)) {
            return new ArrayAdapter($data);
        }

        if (is_iterable($data)) {
            return new ArrayAdapter(iterator_to_array($data));
        }

        throw new Exception('Unable to find an adapter for type "'.\gettype($data).'"');
    }
}
