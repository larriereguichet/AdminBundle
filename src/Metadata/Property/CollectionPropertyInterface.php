<?php

namespace LAG\AdminBundle\Metadata\Property;

interface CollectionPropertyInterface extends PropertyInterface
{
    public function getPropertyType(): PropertyInterface;

    public function withPropertyType(PropertyInterface $property): self;
}
