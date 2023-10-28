<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Parameter;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ParametersMapper
{
    /**
     * Map a list of parameters to properties of the given object.
     *
     * @param object $data Data use to fill parameters
     * @param array<int, string> $routeParameters The list of the parameter names to map
     *
     * @return array<string, string> Mapped parameters
     */
    public function map(object $data, array $routeParameters = []): array
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $mappedRouteParameters = [];

        foreach ($routeParameters as $parameter) {
            $mappedRouteParameters[$parameter] = $accessor->getValue($data, $parameter);
        }

        return $mappedRouteParameters;
    }
}
