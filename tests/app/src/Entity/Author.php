<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GeneratedValue;

#[ORM\Entity]
#[ORM\Table]
#[\LAG\AdminBundle\Metadata\Resource(
    operations: [
        new \LAG\AdminBundle\Metadata\Index(grid: 'authors'),
        new \LAG\AdminBundle\Metadata\Create(),
        new \LAG\AdminBundle\Metadata\Update(),
        new \LAG\AdminBundle\Metadata\Delete(),
        new \LAG\AdminBundle\Metadata\Show(),
    ]),
]
#[\LAG\AdminBundle\Metadata\Grid(name: 'authors')]
class Author
{
    #[ORM\Id]
    #[ORM\Column]
    #[GeneratedValue(strategy: 'AUTO')]
    #[\LAG\AdminBundle\Metadata\Text(label: false)]
    public ?int $id = null;

    #[ORM\Column]
    #[\LAG\AdminBundle\Metadata\Text]
    public ?string $name = null;
}
