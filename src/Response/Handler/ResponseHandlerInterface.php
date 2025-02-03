<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ResponseHandlerInterface
{
    public function supports(OperationInterface $operation, mixed $data, Request $request, array $context = []): bool;

    public function createResponse(OperationInterface $operation, mixed $data, Request $request, array $context = []): Response;
}
