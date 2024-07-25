<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Metadata;

interface ConfigurablePropertyInterface
{
    public function configure(mixed $data): void;
}
