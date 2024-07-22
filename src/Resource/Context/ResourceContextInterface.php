<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Retrieve the current resource and operation for the given request.
 */
interface ResourceContextInterface
{
    public function getResource(Request $request): Resource;

    public function getOperation(Request $request): OperationInterface;

    public function supports(Request $request): bool;
}
