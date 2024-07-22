<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Registry;

use LAG\AdminBundle\Resource\Metadata\Application;

interface ApplicationRegistryInterface
{
    public function get(string $name): Application;

    public function has(string $name): bool;
}
