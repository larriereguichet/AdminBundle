<?php

namespace LAG\AdminBundle\Admin;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;

interface ManagerInterface
{
    public function __construct(EntityManager $manager, ObjectRepository $repository);

    public function find($id);

    public function findAll();

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    public function findOneBy(array $criteria);

    public function getClassName();
}
