<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GeneratedValue;

#[ORM\Entity]
#[ORM\Table]
#[\LAG\AdminBundle\Metadata\Attribute\Resource(
    operations: [
        new \LAG\AdminBundle\Metadata\Attribute\Index(grid: 'authors'),
        new \LAG\AdminBundle\Metadata\Attribute\Create(),
        new \LAG\AdminBundle\Metadata\Attribute\Update(),
        new \LAG\AdminBundle\Metadata\Attribute\Delete(),
        new \LAG\AdminBundle\Metadata\Attribute\Show(),
    ]),
]
#[\LAG\AdminBundle\Metadata\Attribute\Grid(name: 'authors')]
class Author
{
    #[ORM\Id]
    #[ORM\Column]
    #[GeneratedValue(strategy: 'AUTO')]
    #[\LAG\AdminBundle\Metadata\Attribute\Text(label: false)]
    public ?int $id = null;

    #[ORM\Column]
    #[\LAG\AdminBundle\Metadata\Attribute\Text]
    public ?string $name = null;
}
