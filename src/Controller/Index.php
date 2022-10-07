<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Exception\Operation\InvalidCollectionOperationException;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\State\DataProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Index
{
    public function __construct(
        private UriVariablesExtractorInterface $uriVariablesExtractor,
        private ContextProviderInterface $contextProvider,
        private DataProviderInterface $dataProvider,
        private FormFactoryInterface $formFactory,
        private GridFactoryInterface $gridFactory,
        private Environment $environment,
    ) {
    }

    public function __invoke(Request $request, AdminResource $resource): Response
    {
        $operation = $this->getOperation($resource);
        $uriVariables = $this->uriVariablesExtractor->extractVariables($operation, $request);
        $context = $this->contextProvider->getContext($operation, $request);
        $form = null;

        if ($operation->getFormType()) {
            $form = $this->formFactory->create($operation->getFormType(), [], $operation->getFormOptions());
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $context['filters'] = $form->getData();
            }
        }
        $data = $this->dataProvider->provide($operation, $uriVariables, $context);
        $grid = $this->gridFactory->create($operation, $data);

        return new Response($this->environment->render($operation->getTemplate(), [
            'grid' => $grid,
            'resource' => $resource,
            'operation' => $operation,
            'data' => $data,
            'form' => $form?->createView(),
        ]));
    }

    private function getOperation(AdminResource $resource): CollectionOperationInterface
    {
        $operation = $resource->getCurrentOperation();

        if (!$operation instanceof CollectionOperationInterface) {
            throw new InvalidCollectionOperationException($operation);
        }

        return $operation;
    }
}
