<?php

namespace LAG\AdminBundle\Tests\Entity;

class TestEntity
{
    protected $id;

    protected $name;

    /**
     * TestEntity constructor.
     *
     * @param null $id
     * @param string $name
     */
    public function __construct($id = null, $name = '')
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
}
