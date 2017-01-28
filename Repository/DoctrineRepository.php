<?php

namespace LAG\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Abstract doctrine repository
 */
abstract class DoctrineRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * Save an entity.
     *
     * @param $entity
     */
    public function save($entity)
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }
    /**
     * Delete an entity.
     *
     * @param $entity
     */
    public function delete($entity)
    {
        $this->_em->remove($entity);
        $this->_em->flush();
    }
}
