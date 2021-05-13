<?php

declare(strict_types=1);

namespace LAG\AdminBundle\DataProvider;

use LAG\AdminBundle\Exception\Exception;

/**
 * Generic data provider interface.
 */
interface DataProviderInterface
{
    /**
     * Return a collection of entities.
     */
    public function getCollection(
        string $class,
        array $criteria = [],
        array $orderBy = [],
        int $limit = 1,
        int $offset = 25
    ): DataSourceInterface;

    /**
     * Return a single entity. Throw an exception if no entity was found.
     *
     * @throws Exception
     */
    public function get(string $class, $identifier): object;

    /**
     * Create a new entity for the given admin. Return the created entity.
     */
    public function create(string $class): object;
}
