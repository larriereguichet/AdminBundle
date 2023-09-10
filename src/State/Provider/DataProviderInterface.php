<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Provider;

use LAG\AdminBundle\Metadata\OperationInterface;

interface DataProviderInterface
{
    /**
     * Return data for the given operation and resource. Variables configured in the operation are provided in the
     * $uriVariables. An additional context could be added.
     *
     * @param OperationInterface $operation The current operation according to the current request url and method
     * @param array<string, mixed> $uriVariables Variables (for example identifiers) extracted from the request path
     * @param array<string, mixed> $context Additional context
     *
     * @return mixed Data returned by the data source (database, messenger...)
     */
    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed;
}
