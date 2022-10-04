<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State;

use LAG\AdminBundle\Metadata\OperationInterface;

interface DataProviderInterface
{
    public function provide(OperationInterface $operation, array $uriVariables = [], array $context = []): mixed;
}
