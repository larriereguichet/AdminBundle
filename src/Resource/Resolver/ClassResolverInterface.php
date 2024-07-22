<?php

namespace LAG\AdminBundle\Resource\Resolver;

interface ClassResolverInterface
{
    public function resolveClass(string $path): ?\ReflectionClass;
}
