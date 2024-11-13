<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LAG\AdminBundle\Tests\Application\Entity\Book;

final class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
}