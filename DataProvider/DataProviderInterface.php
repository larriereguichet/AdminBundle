<?php

namespace LAG\AdminBundle\DataProvider;

interface DataProviderInterface
{
    public function save($entity);

    public function delete($entity);

    public function find(array $criteria, $orderBy = [], $limit = null, $offset = null);
}
