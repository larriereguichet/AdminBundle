<?php

namespace LAG\AdminBundle\Tests\Fixtures;

class EntityFixture
{
    protected $id;

    public function __construct(string $id = null)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
