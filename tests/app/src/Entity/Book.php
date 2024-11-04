<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use LAG\AdminBundle\Resource\Metadata as LAG;

#[LAG\Resource(
    name: 'book',
    pathPrefix: '/books',
)]
#[Entity]
#[Table]
class Book
{
    #[ORM\Id]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column]
    public ?string $name = null;

    #[ORM\Column]
    public ?string $isbn = null;
}