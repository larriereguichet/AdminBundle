<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Tests\Fixtures\Entity\TestEntity;
use stdClass;

class DoctrineRepositoryTest extends AdminTestBase
{
    public function testSave()
    {
        $entity = new TestEntity();

        $entityManager = $this->getMockWithoutConstructor(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($entity)
        ;
        $entityManager
            ->expects($this->once())
            ->method('flush')
            ->with()
        ;

        $metadata = $this->getMockWithoutConstructor(ClassMetadata::class);

        $repository = new FakeRepository($entityManager, $metadata, TestEntity::class);

        $repository->save($entity);
    }

    public function testSaveWrongEntity()
    {
        $entity = new stdClass();

        $entityManager = $this->getMockWithoutConstructor(EntityManager::class);
        $entityManager
            ->expects($this->never())
            ->method('persist')
            ->with($entity)
        ;
        $entityManager
            ->expects($this->never())
            ->method('flush')
            ->with()
        ;

        $metadata = $this->getMockWithoutConstructor(ClassMetadata::class);

        $repository = new FakeRepository($entityManager, $metadata, TestEntity::class);

        $this->assertExceptionRaised(\LogicException::class, function () use ($repository, $entity) {
            $repository->save($entity);
        });
    }

    public function testDelete()
    {
        $entity = new TestEntity();

        $entityManager = $this->getMockWithoutConstructor(EntityManager::class);
        $entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($entity)
        ;
        $entityManager
            ->expects($this->once())
            ->method('flush')
            ->with()
        ;

        $metadata = $this->getMockWithoutConstructor(ClassMetadata::class);

        $repository = new FakeRepository($entityManager, $metadata, TestEntity::class);

        $repository->delete($entity);
    }

    public function testDeleteWrongEntity()
    {
        $entity = new stdClass();

        $entityManager = $this->getMockWithoutConstructor(EntityManager::class);
        $entityManager
            ->expects($this->never())
            ->method('remove')
            ->with($entity)
        ;
        $entityManager
            ->expects($this->never())
            ->method('flush')
            ->with()
        ;

        $metadata = $this->getMockWithoutConstructor(ClassMetadata::class);

        $repository = new FakeRepository($entityManager, $metadata, TestEntity::class);

        $this->assertExceptionRaised(\LogicException::class, function () use ($repository, $entity) {
            $repository->delete($entity);
        });
    }
}
