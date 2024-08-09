<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Resolver;

interface ClassResolverInterface
{
    public function resolveClass(string $path): ?\ReflectionClass;
}
