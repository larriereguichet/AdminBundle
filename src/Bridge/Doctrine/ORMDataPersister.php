<?php

namespace LAG\AdminBundle\Bridge\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use LAG\AdminBundle\DataPersister\DataPersisterInterface;

class ORMDataPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save($data): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function delete($data): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
