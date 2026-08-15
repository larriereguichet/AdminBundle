<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\Mapper;

interface ParametersMapperInterface
{
    /**
     * Map route parameters coming usually from current operation router parameters, to a object or array of values.
     * Each route parameter will me mapped to value from data.
     *
     * @param mixed $data Data use to fill parameters
     * @param array<int|string, string|null> $routeParameters The list of the parameter names to map
     *
     * @return array<string, string> Mapped parameters
     */
    public function mapObjectToRouteParameters(mixed $data, array $routeParameters = []): array;
}
