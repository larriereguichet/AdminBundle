<?php

namespace LAG\AdminBundle\Field;

interface EntityAwareInterface
{
    /**
     * Defines entiy for field.
     *
     * @param $entity
     * @return void
     */
    public function setEntity($entity);
}
