<?php

namespace LAG\AdminBundle\Tests\Entity;

class TestSimpleEntity
{
    protected $id;

    protected $name;

    /**
     * TestEntity constructor.
     *
     * @param integer $id
     * @param string $name
     */
    public function __construct($id = null, $name = '')
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return integer
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
