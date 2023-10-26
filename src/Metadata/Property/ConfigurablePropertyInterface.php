<?php

namespace LAG\AdminBundle\Metadata\Property;

interface ConfigurablePropertyInterface
{
    public function configure(mixed $data): void;
}
