<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Grid\Builder\GridViewBuilderInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Response\Handler\RedirectHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private RedirectHandlerInterface $redirectionHandler,
        private GridViewBuilderInterface $gridBuilder,
        private FormFactoryInterface $formFactory,
        private SerializerInterface $serializer,
        private Environment $environment,
    ) {
    }

    public function __invoke(Request $request, CollectionOperationInterface $operation): Response
    {
        $uriVariables = $this->uriVariablesExtractor->extractVariables($operation, $request);
        $context = $this->contextProvider->getContext($operation, $request);
        $filterForm = null;

        if ($operation->getFilterFormType() !== null) {
            $filterForm = $this->formFactory->create($operation->getFilterFormType(), [], $operation->getFilterFormOptions());
            $filterForm->handleRequest($request);
        }
        $data = $this->provider->provide($operation, $uriVariables, $context);
        $form = null;

        if ($operation->getFormType() !== null) {
            $form = $this->formFactory->create(CollectionType::class, $data->getCurrentPageResults(), [
                'entry_type' => $operation->getFormType(),
                'entry_options' => $operation->getFormOptions(),
            ]);
            $form->handleRequest($request);

            if ($context['json'] ?? false) {
                $form->submit($request->toArray());
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $this->processor->process($data, $operation, $uriVariables, $context);

                return $this->redirectionHandler->createRedirectResponse($operation, $data, $context);
            }
        }

        if ($operation->getGrid() !== null) {
            $grid = $this->gridBuilder->build(
                $operation->getGrid(),
                $operation,
                $data,
                $form?->createView(),
                $context
            );
        }

        if ($context['json'] ?? false) {
            $content = $this->serializer->serialize($data, 'json', $operation->getNormalizationContext());

            return new JsonResponse($content, Response::HTTP_OK, [], true);
        }

        return new Response($this->environment->render($operation->getTemplate(), [
            'grid' => $grid ?? null,
            'resource' => $operation->getResource(),
            'operation' => $operation,
            'data' => $data,
            'filterForm' => $filterForm?->createView(),
            'form' => $form?->createView(),
        ]));
    }
}
