<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

interface CompoundPropertyInterface extends PropertyInterface
{
    /** @return iterable<string>  */
    public function getProperties(): iterable;

    public function withProperties(array $properties): self;
}
