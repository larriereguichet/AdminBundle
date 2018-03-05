<?php

namespace LAG\AdminBundle\Field;

interface EntityAwareFieldInterface
{
    /**
     * Defines the entity for the field.
     *
     * @param $entity
     */
    public function setEntity($entity);
}
