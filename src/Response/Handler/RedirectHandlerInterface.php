<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Response;

interface RedirectHandlerInterface
{
    public function createRedirectResponse(OperationInterface $operation, mixed $data): Response;
}
