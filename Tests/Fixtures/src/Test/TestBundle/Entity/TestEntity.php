<?php

namespace Test\TestBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="entity_test")
 * @ORM\Entity(repositoryClass="Test\TestBundle\Entity\TestEntityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TestEntity
{
    /**
     * Entity id
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * Return entity id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set entity id
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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

    /**
     * @ORM\PrePersist()
     * @return $this
     */
    public function setCreatedAt()
    {
        if (!$this->createdAt) {
            $this->createdAt = new DateTime();
        }
    }

    /**
     * Created at cannot be set. But in some case (like imports...), it is required to set created at. Use this method
     * in this case
     *
     * @param DateTime $createdAt
     */
    public function forceCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @param null $value
     * @return $this
     */
    public function setUpdatedAt($value = null)
    {
        if ($value instanceof DateTime) {
            $this->updatedAt = $value;
        } else {
            $this->updatedAt = new DateTime();
        }
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
