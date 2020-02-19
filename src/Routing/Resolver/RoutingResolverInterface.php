<?php

namespace LAG\AdminBundle\Routing\Resolver;

interface RoutingResolverInterface
{
    public function resolve(string $adminName, string $actionName): string;

    public function resolveOptions(array $options): ?string;
}
