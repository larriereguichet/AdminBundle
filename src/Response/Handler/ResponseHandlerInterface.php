<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Response\Handler;

use LAG\AdminBundle\Grid\View\GridView;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ResponseHandlerInterface
{
    public function createResponse(
        Request $request,
        OperationInterface $operation,
        mixed $data,
        ?FormInterface $form = null,
        ?FormInterface $filterForm = null,
        ?GridView $grid = null,
    ): Response;

    public function createRedirectResponse(OperationInterface $operation, mixed $data, array $context = []): Response;
}
