<?php

namespace LAG\AdminBundle\Field\Traits;

trait EntityAwareTrait
{
    /**
     * @var mixed
     */
    protected $entity;

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
}
