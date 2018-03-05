<?php

namespace LAG\AdminBundle\DataProvider;

/**
 * Generic data provider interface
 */
interface DataProviderInterface
{
    public function getCollection(string $entityClass);
}
