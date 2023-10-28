<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\State\Provider\DataProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;

readonly class ResourceCollectionController
{
    public function __construct(
        private UriVariablesExtractorInterface $uriVariablesExtractor,
        private ContextProviderInterface $contextProvider,
        private DataProviderInterface $dataProvider,
        private GridFactoryInterface $gridFactory,
        private FormFactoryInterface $formFactory,
        private SerializerInterface $serializer,
        private Environment $environment,
    ) {
    }

    public function __invoke(Request $request, CollectionOperationInterface $operation): Response
    {
        $uriVariables = $this->uriVariablesExtractor->extractVariables($operation, $request);
        $context = $this->contextProvider->getContext($operation, $request);
        $form = null;

        if ($operation->getFilterFormType() !== null) {
            $form = $this->formFactory->create($operation->getFilterFormType(), [], $operation->getFilterFormOptions());
            $form->handleRequest($request);
        }
        $data = $this->dataProvider->provide($operation, $uriVariables, $context);
        $grid = $this->gridFactory->create($operation, $data);

        if ($context['json']) {
            $content = $this->serializer->serialize($data, 'json', $operation->getNormalizationContext());

            return new JsonResponse($content, Response::HTTP_OK, [], true);
        }

        return new Response($this->environment->render($operation->getTemplate(), [
            'grid' => $grid,
            'resource' => $operation->getResource(),
            'operation' => $operation,
            'data' => $data,
            'form' => $form?->createView(),
        ]));
    }
}
