<?php

namespace LAG\AdminBundle\Resource\Resolver;

interface ClassResolverInterface
{
    /** @return iterable<\ReflectionClass> */
    public function resolveClasses(string $directory): iterable;
}
