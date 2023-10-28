<?php

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RedirectHandlerInterface
{
    public function createRedirectResponse(OperationInterface $operation, mixed $data): Response;
}
