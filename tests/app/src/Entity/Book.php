<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use LAG\AdminBundle\Tests\Application\Repository\BookRepository;
use LAG\AdminBundle\Tests\Application\State\Provider\Book\LatestBookProvider;
use \LAG\AdminBundle\Metadata\Attribute as LAG;

#[LAG\Resource(
    shortName: 'book',
    pathPrefix: '/books',
    operations: [
        new LAG\Index(grid: 'projects_table'),
        new LAG\Show(),
        new LAG\Show(
            name: 'latest',
            path: '/latest',
            provider: LatestBookProvider::class
        ),
    ],
)]
#[LAG\Grid(
    name: 'projects_table',
    title: 'Books',
    properties: ['id', 'name', 'isbn', 'show']
)]
#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Table('book')]
class Book
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[LAG\Link(propertyPath: true, label: false, operation: 'show', textPath: 'id')]
    #[LAG\Link(name: 'show', propertyPath: true, label: 'actions', operation: 'show', text: 'Show book')]
    public ?int $id = null;

    #[ORM\Column]
    #[LAG\Text]
    public ?string $name = null;

    #[ORM\Column]
    #[LAG\Text]
    public ?string $isbn = null;
}
