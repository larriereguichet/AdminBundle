<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Table;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Metadata as LAG;
use LAG\AdminBundle\Tests\Application\Repository\BookRepository;
use LAG\AdminBundle\Tests\Application\State\Provider\Book\LatestBookProvider;

#[LAG\Resource(
    name: 'book',
    pathPrefix: '/books',
    operations: [
        new Index(grid: 'projects_table'),
        new Show(),
        new Show(
            name: 'latest',
            path: '/latest',
            provider: LatestBookProvider::class
        ),
    ],
)]
#[LAG\Grid(
    name: 'projects_table',
    title: 'Books',
    properties: ['id', 'name', 'isbn']
)]
#[Entity(repositoryClass: BookRepository::class)]
#[Table]
class Book
{
    #[ORM\Id]
    #[ORM\Column]
    #[GeneratedValue(strategy: 'AUTO')]
    #[LAG\Text]
    public ?int $id = null;

    #[ORM\Column]
    #[LAG\Text]
    public ?string $name = null;

    #[ORM\Column]
    #[LAG\Text]
    public ?string $isbn = null;
}