<?php

namespace LAG\AdminBundle\Metadata\Property;

interface CompositePropertyInterface extends PropertyInterface
{
    /**
     * @return array<string, PropertyInterface>
     */
    public function getChildren(): array;

    public function withChildren(array $children): self;
}
