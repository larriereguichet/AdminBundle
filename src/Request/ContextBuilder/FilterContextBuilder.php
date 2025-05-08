<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\ContextBuilder;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class FilterContextBuilder implements ContextBuilderInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function supports(OperationInterface $operation, Request $request): bool
    {
        return $operation instanceof CollectionOperationInterface && $operation->getFilterForm() !== null;
    }

    /** @param CollectionOperationInterface $operation */
    public function buildContext(OperationInterface $operation, Request $request): array
    {
        $context = [];
        $form = $this->formFactory->create($operation->getFilterForm(), [], $operation->getFilterFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $context['filters'] = $form->getData();
        }
        $context['filterForm'] = $form;

        return $context;
    }
}
