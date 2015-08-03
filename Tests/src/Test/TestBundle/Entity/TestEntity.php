<?php

namespace Test\TestBundle\Entity;

use BlueBear\BaseBundle\Entity\Behaviors\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="entity_test")
 * @ORM\Entity(repositoryClass="Test\TestBundle\Entity\TestEntityRepository")
 */
class TestEntity
{
    use Id;
}
