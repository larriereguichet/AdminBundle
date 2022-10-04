<?php

namespace LAG\AdminBundle\Routing\Parameter;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ParametersMapper
{
    public function map(object $data, array $routeParameters = []): array
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $mappedRouteParameters = [];

        foreach ($routeParameters as $parameter) {
            $mappedRouteParameters[$parameter] = $accessor->getValue($data, (string)$parameter);
        }

        return $mappedRouteParameters;
    }
}
