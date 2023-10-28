<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Context;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;

interface ResourceContextInterface
{
    public function getResource(Request $request): AdminResource;

    public function getOperation(Request $request): OperationInterface;

    public function supports(Request $request): bool;
}
