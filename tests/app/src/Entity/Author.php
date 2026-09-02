<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Application\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GeneratedValue;
use LAG\AdminBundle\Metadata\Attribute as LAG;

#[ORM\Entity]
#[ORM\Table]
#[LAG\Resource(
    operations: [
        new LAG\Index(grid: 'authors'),
        new LAG\Create(),
        new LAG\Update(),
        new LAG\Delete(),
        new LAG\Show(),
    ]),
]
#[LAG\Grid(name: 'authors')]
class Author
{
    #[ORM\Id]
    #[ORM\Column]
    #[GeneratedValue(strategy: 'AUTO')]
    #[LAG\Text(label: false)]
    public ?int $id = null;

    #[ORM\Column]
    #[LAG\Text]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[LAG\RichText]
    public ?string $biography = null;
}
