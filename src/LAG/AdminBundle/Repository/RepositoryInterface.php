<?php

namespace LAG\AdminBundle\Repository;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;

interface RepositoryInterface extends ObjectRepository
{
    /**
     * Find an entity by its identifier
     *
     * @param $id
     * @return object
     */
    public function find($id);

    /**
     * Find all entities in the repository
     *
     * @return array|Collection
     */
    public function findAll();
    
    /**
     * Find all entities matching criteria
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array|Collection
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * Find one entity matching criteria
     *
     * @param array $criteria
     * @return object
     */
    public function findOneBy(array $criteria);

    /**
     * Save an entity
     *
     * @param $entity
     */
    public function save($entity);

    /**
     * Delete an entity
     *
     * @param object $entity
     */
    public function delete($entity);
    
    /**
     * Return repository class name
     *
     * @return string
     */
    public function getClassName();
    
    /**
     * Return the number of entities in the Repository.
     *
     * @param array $criteria
     * @param array $options
     *
     * @return int
     */
    public function count(array $criteria = [], array $options = []);
}
