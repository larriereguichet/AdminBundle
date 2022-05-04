<?php

namespace LAG\AdminBundle\Routing\Parameter;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ParametersMapper
{
    public function map(object $data, array $routeParameters = []): array
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $mappedRouteParameters = [];

        foreach ($routeParameters as $parameter => $requirements) {
            $mappedRouteParameters[$requirements] = $accessor->getValue($data, (string)$requirements);
        }

        return $mappedRouteParameters;
    }
}
