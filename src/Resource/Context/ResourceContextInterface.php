<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\HttpFoundation\Request;

interface ResourceContextInterface
{
    public function getResource(Request $request): Resource;

    public function getOperation(Request $request): OperationInterface;

    public function supports(Request $request): bool;
}
