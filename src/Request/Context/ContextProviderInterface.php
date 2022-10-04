<?php

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

interface ContextProviderInterface
{
    public function getContext(OperationInterface $operation, Request $request): array;
}
