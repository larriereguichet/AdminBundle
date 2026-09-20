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
        // Routed outside the ^/admin firewall on purpose: nothing but the operation permissions guards
        // it, which is the configuration the access listener is supposed to cover.
        new LAG\Show(name: 'secured', path: '/authors/{id}/secured', permissions: ['ROLE_ADMIN']),
        new LAG\Show(name: 'granted', path: '/authors/{id}/granted', permissions: ['PUBLIC_ACCESS']),
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
