<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;
use LAG\AdminBundle\Doctrine\Repository\DoctrineRepository;

class FakeRepository extends DoctrineRepository
{
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class, $className = '')
    {
        parent::__construct($em, $class);
        $this->_entityName = $className;
    }
}
