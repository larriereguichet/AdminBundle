<?php

namespace LAG\AdminBundle\Controller;

use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Routing\UrlGenerator\UrlGeneratorInterface;
use LAG\AdminBundle\State\DataProcessorInterface;
use LAG\AdminBundle\State\DataProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AbstractOperationController
{
    public function __construct(
        private UriVariablesExtractorInterface $uriVariablesExtractor,
        private ContextProviderInterface $contextProvider,
        private DataProviderInterface $dataProvider,
        private DataProcessorInterface $dataProcessor,
        private FormFactoryInterface $formFactory,
        private Environment $environment,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(Request $request, Admin $resource): Response
    {
        $operation = $resource->getCurrentOperation();
        $uriVariables = $this->uriVariablesExtractor->extractVariables($operation, $request);
        $context = $this->contextProvider->getContext($operation, $request);
        $data = $this->dataProvider->provide($operation, $uriVariables, $context);
        $form = null;

        if ($operation->getFormType()) {
            $form = $this->formFactory->create($operation->getFormType(), $data, $operation->getFormOptions());
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $this->dataProcessor->process($data, $operation, $uriVariables, $context);

                return new RedirectResponse($this->urlGenerator->generateFromRouteName(
                    $operation->getTargetRoute(),
                    $operation->getRouteParameters(),
                ));
            }
        }

        return new Response($this->environment->render($operation->getTemplate(), [
            'resource' => $resource,
            'operation' => $operation,
            'data' => $data,
            'form' => $form?->createView(),
        ]));
    }
}