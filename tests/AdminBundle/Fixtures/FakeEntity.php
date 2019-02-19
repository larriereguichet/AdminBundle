<?php

namespace LAG\AdminBundle\Tests\Fixtures;

class FakeEntity
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
