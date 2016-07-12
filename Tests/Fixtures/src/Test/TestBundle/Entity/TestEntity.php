<?php

namespace Test\TestBundle\Entity;

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

    protected $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
