<?php

namespace LAG\AdminBundle\Tests\Entity;

use BlueBear\BaseBundle\Entity\Behaviors\Id;
use BlueBear\BaseBundle\Entity\Behaviors\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="entity_test")
 * @ORM\Entity(repositoryClass="Test\TestBundle\Entity\TestEntityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TestEntity
{
    use Id, Timestampable;
}
