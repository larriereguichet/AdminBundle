<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\State\Provider\Book;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use LAG\AdminBundle\Tests\Application\Entity\Book;
use LAG\AdminBundle\Tests\Application\Repository\BookRepository;

// Tests for provider and operations without identifiers
final readonly class LatestBookProvider implements ProviderInterface
{
    public function __construct(
        private BookRepository $bookRepository,
    ) {
    }

    public function provide(OperationInterface $operation, array $urlVariables = [], array $context = []): ?Book
    {
        return $this->bookRepository->createQueryBuilder('books')
            ->orderBy('books.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
