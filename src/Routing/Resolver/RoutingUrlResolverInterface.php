<?php

namespace LAG\AdminBundle\Routing\Resolver;

interface RoutingUrlResolverInterface
{
    /**
     * Return an url according to given link options. The link options has to be resolved with ActionLinkConfiguration
     * before calling this method. Data can be passed to resolve dynamic route parameters.
     */
    public function resolve(array $linkOptions, object $data = null): string;
}
