<?php

namespace LAG\AdminBundle\Field;

interface EntityFieldInterface
{
    /**
     * Defines entiy for field.
     *
     * @param $entity
     * @return void
     */
    public function setEntity($entity);
}
