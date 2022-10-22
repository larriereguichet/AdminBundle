<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Resolver;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Routing\Route\RouteNameGeneratorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;

class RoutingUrlResolver implements RoutingUrlResolverInterface
{
    public function __construct(
        private RouterInterface $router,
        private UrlGeneratorInterface $urlGenerator,
        private RouteNameGeneratorInterface $routeNameGenerator,
    ) {
    }

    public function resolve(array $linkOptions, object $data = null): string
    {
        // TODO remove ?
        // Url has most priority, if it is defined, it becomes the link url
        if ($linkOptions['url'] !== null) {
            return $linkOptions['url'];
        }

        if ($linkOptions['route'] !== null) {
            return $this->router->generate(
                $linkOptions['route'],
                $this->resolveRouteParameters($linkOptions['route_parameters'], $data),
            );
        }

        if ($linkOptions['admin'] !== null && $linkOptions['action'] !== null) {
            $routeName = $this->routeNameGenerator->generateRouteName(
                $linkOptions['admin'],
                $linkOptions['action']
            );
            $route = $this->router->getRouteCollection()->get($routeName);

            return $this->urlGenerator->generatePath(
                $linkOptions['admin'],
                $linkOptions['action'],
                $route->getRequirements(),
                $data,
            );
        }

        throw new Exception(sprintf('Unable to resolve an url for the link with data "%s"', print_r($linkOptions, true)));
    }

    private function resolveRouteParameters(array $routeParameters, object $data = null): array
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $resolved = [];

        foreach ($routeParameters as $property => $value) {
            if ($value !== null) {
                $resolved[$property] = $value;
            } else {
                if ($data === null) {
                    throw new Exception(sprintf('The url parameter "%s" can not be resolved as passed data is null', $property));
                }
                $resolved[$property] = $accessor->getValue($data, $property);
            }
        }

        return $resolved;
    }
}
