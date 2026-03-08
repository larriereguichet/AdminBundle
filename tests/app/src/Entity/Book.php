<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Table;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Tests\Application\Repository\BookRepository;
use LAG\AdminBundle\Tests\Application\State\Provider\Book\LatestBookProvider;

#[\LAG\AdminBundle\Metadata\Attribute\Resource(
    shortName: 'book',
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
#[\LAG\AdminBundle\Metadata\Attribute\Grid(
    name: 'projects_table',
    title: 'Books',
    properties: ['id', 'name', 'isbn', 'show']
)]
#[Entity(repositoryClass: BookRepository::class)]
#[Table]
class Book
{
    #[ORM\Id]
    #[ORM\Column]
    #[GeneratedValue(strategy: 'AUTO')]
    #[\LAG\AdminBundle\Metadata\Attribute\Link(
        propertyPath: true,
        label: false,
        operation: 'show',
        textPath: 'id',
    )]
    #[\LAG\AdminBundle\Metadata\Attribute\Link(
        name: 'show',
        propertyPath: true,
        label: 'actions',
        operation: 'show',
        text: 'Show book'
    )]
    public ?int $id = null;

    #[ORM\Column]
    #[\LAG\AdminBundle\Metadata\Attribute\Text]
    public ?string $name = null;

    #[ORM\Column]
    #[\LAG\AdminBundle\Metadata\Attribute\Text]
    public ?string $isbn = null;
}
