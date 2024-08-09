<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Locator;

interface PropertyLocatorInterface
{
    public function locateProperties(\ReflectionClass $resourceClass): array;
}
