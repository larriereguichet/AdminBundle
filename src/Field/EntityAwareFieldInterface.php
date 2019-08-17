<?php

namespace LAG\AdminBundle\Field;

interface EntityAwareFieldInterface extends FieldInterface
{
    /**
     * Defines the entity for the field.
     *
     * @param mixed $entity
     */
    public function setEntity($entity);
}
