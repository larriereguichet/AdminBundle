<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Factory\FieldFactoryInterface;

interface FieldFactoryAwareInterface
{
    public function setRenderer(FieldFactoryInterface $factory);
}
