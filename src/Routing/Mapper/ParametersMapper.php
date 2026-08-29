<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Mapper;

use Symfony\Component\PropertyAccess\PropertyAccess;

final readonly class ParametersMapper implements ParametersMapperInterface
{
    public function mapObjectToRouteParameters(mixed $data, array $routeParameters = []): array
    {
        if ($data === null || \count($routeParameters) === 0) {
            return [];
        }
        $accessor = PropertyAccess::createPropertyAccessor();
        $mappedRouteParameters = [];

        foreach ($routeParameters as $parameter => $propertyPath) {
            $propertyPath ??= $parameter;

            if (\is_int($parameter)) {
                $parameter = $propertyPath;
            }

            if (\is_array($data)) {
                $propertyPath = '['.$propertyPath.']';
            }
            $mappedRouteParameters[$parameter] = $accessor->getValue($data, $propertyPath);
        }

        return $mappedRouteParameters;
    }
}
