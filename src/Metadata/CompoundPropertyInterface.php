<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface CompoundPropertyInterface extends PropertyInterface
{
    /** @return iterable<string> */
    public function getProperties(): iterable;

    /** @param array<string> $properties */
    public function withProperties(array $properties): self;
}
