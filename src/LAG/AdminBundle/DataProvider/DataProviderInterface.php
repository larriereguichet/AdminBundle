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
     * @param null|int  $limit
     * @param null|int  $offset
     * @param array $options
     *
     * @return mixed
     */
    public function findBy(array $criteria = [], $orderBy = [], $limit = null, $offset = null, array $options = []);

    /**
     * Find an entity according to its unique id.
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id);
    
    /**
     * Return the total number of entities managed by the DataProvider.
     *
     * @param array $criteria
     *
     * @param array $options
     *
     * @return int
     */
    public function count(array $criteria = [], array $options = []);
}
