<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Table;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Tests\Application\Repository\BookRepository;
use LAG\AdminBundle\Tests\Application\State\Provider\Book\LatestBookProvider;

#[Resource(
    name: 'book',
    pathPrefix: '/books',
    operations: [
        new Index(),
        new Show(),
        new Show(
            name: 'latest',
            path: '/latest',
            provider: LatestBookProvider::class
        ),
    ],
)]
#[Entity(repositoryClass: BookRepository::class)]
#[Table]
class Book
{
    #[ORM\Id]
    #[ORM\Column]
    #[GeneratedValue(strategy: 'AUTO')]
    public ?int $id = null;

    #[ORM\Column]
    public ?string $name = null;

    #[ORM\Column]
    public ?string $isbn = null;
}