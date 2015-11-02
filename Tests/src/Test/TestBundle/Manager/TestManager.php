<?php

namespace Test\TestBundle\Manager;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use LAG\AdminBundle\Admin\ManagerInterface;

class TestManager implements ManagerInterface
{
    public function __construct(EntityManager $manager, ObjectRepository $repository)
    {
    }

    public function find($id)
    {
    }

    public function findAll()
    {
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    }

    public function findOneBy(array $criteria)
    {
    }

    public function getClassName()
    {
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function delete($entity)
    {
        // TODO: Implement delete() method.
    }

    public function save($entity)
    {
        // TODO: Implement save() method.
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        // TODO: Implement getRepository() method.
    }
}
