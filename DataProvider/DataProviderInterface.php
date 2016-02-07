<?php

namespace LAG\AdminBundle\DataProvider;

/**
 * Generic data provider interface
 */
interface DataProviderInterface
{
    /**
     * Save an entity.
     *
     * @param $entity
     */
    public function save($entity);

    /**
     * Remove an entity.
     *
     * @param $entity
     */
    public function remove($entity);

    /**
     * Create an new entity.
     *
     * @return mixed
     */
    public function create();

    /**
     * Find entities according to the given criteria.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function findBy(array $criteria = [], $orderBy = [], $limit = null, $offset = null);

    /**
     * Find an entity according to its unique id.
     *
     * @param $id
     * @return mixed
     */
    public function find($id);
}
