<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\HttpFoundation\Response;

interface ContentResponseHandlerInterface
{
    public function createResponse(OperationInterface $operation, mixed $data, array $context = []): Response;
}
