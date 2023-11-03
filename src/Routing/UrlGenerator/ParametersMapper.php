<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ParametersMapper implements ParametersMapperInterface
{
    public function map(mixed $data, array $routeParameters = []): array
    {
        if ($data === null || count($routeParameters) === 0) {
            return [];
        }
        $accessor = PropertyAccess::createPropertyAccessor();
        $mappedRouteParameters = [];

        foreach ($routeParameters as $parameter) {
            $mappedRouteParameters[$parameter] = $accessor->getValue($data, $parameter);
        }

        return $mappedRouteParameters;
    }
}
