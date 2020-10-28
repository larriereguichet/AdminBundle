<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\Results;

interface ResultsHandlerInterface
{
    /**
     * Return the results according to the given data type (Query, QueryBuilder, array or value). If the pagination is
     * enabled, a pager instance will be returned. The pager adapter is choose according to the data type.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function handle($data, bool $pagination, int $page = 1, int $maxPerPage = 25): object;
}
