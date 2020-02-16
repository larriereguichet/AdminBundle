<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Results;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ResultsHandler implements ResultsHandlerInterface
{
    public function handle($data, bool $pagination, int $page = 1, int $maxPerPage = 25)
    {
        if ($pagination) {
            if ($data instanceof QueryBuilder || $data instanceof Query) {
                $adapter = new DoctrineORMAdapter($data, true, true);
            } else {
                $adapter = new ArrayAdapter($data);
            }
            $pager = new Pagerfanta($adapter);
            $pager->setCurrentPage($page);
            $pager->setMaxPerPage($maxPerPage);

            return $pager;
        }

        if ($data instanceof Query) {
            return $data->getResult();
        }

        if ($data instanceof QueryBuilder) {
            return $data->getQuery()->getResult();
        }

        return $data;
    }
}
