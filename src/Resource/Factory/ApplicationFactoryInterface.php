<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Factory;

use LAG\AdminBundle\Metadata\Attribute\Application;

interface ApplicationFactoryInterface
{
    public function create(string $applicationName): Application;
}
