<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class FilterContextProvider implements ContextProviderInterface
{
    public function __construct(
        private ContextProviderInterface $contextProvider,
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function getContext(OperationInterface $operation, Request $request): array
    {
        $context = $this->contextProvider->getContext($operation, $request);

        if (!$operation instanceof CollectionOperationInterface || !$operation->getFilterForm()) {
            return $context;
        }
        $form = $this->formFactory->create($operation->getFilterForm(), [], $operation->getFilterFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $context['filters'] = $form->getData();
        }

        return $context;
    }
}
