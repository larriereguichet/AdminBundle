<?php

namespace LAG\AdminBundle\Metadata\Factory;

interface ResourceResolverInterface
{
    public function resolveResourceCollectionFromLocators(): iterable;
}
