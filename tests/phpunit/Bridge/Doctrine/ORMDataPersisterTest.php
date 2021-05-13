<?php

namespace LAG\AdminBundle\Tests\Bridge\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORMDataPersister;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

class ORMDataPersisterTest extends TestCase
{
    private ORMDataPersister $persister;
    private MockObject $entityManager;

    public function testSave(): void
    {
        $data = new stdClass();
        $this
            ->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($data)
        ;
        $this
            ->entityManager
            ->expects($this->once())
            ->method('flush')
        ;
        $this->persister->save($data);
    }

    public function testDelete(): void
    {
        $data = new stdClass();
        $this
            ->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($data)
        ;
        $this
            ->entityManager
            ->expects($this->once())
            ->method('flush')
        ;
        $this->persister->delete($data);
    }

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->persister = new ORMDataPersister($this->entityManager);
    }
}
