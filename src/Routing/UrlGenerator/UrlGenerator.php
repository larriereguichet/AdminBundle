<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public function generateUrl(
        string $routeName,
        array $routeParameters = [],
        mixed $data = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        $mappedRouteParameters = $routeParameters;

        if ($data !== null) {
            $mappedRouteParameters = new ParametersMapper()->map($data, $routeParameters);
        }

        return $this->router->generate($routeName, $mappedRouteParameters, $referenceType);
    }
}
