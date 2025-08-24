<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

interface ParametersMapperInterface
{
    /**
     * Map a list of parameters to properties of the given object.
     *
     * @param mixed $data Data use to fill parameters
     * @param array<int|string, string|null> $routeParameters The list of the parameter names to map
     *
     * @return array<string, string> Mapped parameters
     */
    public function map(mixed $data, array $routeParameters = []): array;
}
