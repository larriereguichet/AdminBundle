<?php

namespace Test\TestBundle\Manager;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
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
}
