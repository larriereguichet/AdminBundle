<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Request\Context;

use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class FilterContextProvider implements ContextProviderInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function getContext(OperationInterface $operation, Request $request): array
    {
        if (!$operation instanceof CollectionOperationInterface || !$operation->getFilterFormType()) {
            return [];
        }
        $form = $this->formFactory->create($operation->getFilterFormType(), [], $operation->getFilterFormOptions());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return ['filters' => $form->getData()];
        }

        return [];
    }
}
