<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Property;

interface ConfigurablePropertyInterface
{
    public function configure(mixed $data): void;
}
