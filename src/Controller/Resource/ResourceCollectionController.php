<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Request\Context\ContextProviderInterface;
use LAG\AdminBundle\Request\Uri\UriVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
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

final readonly class ResourceCollectionController
{
    public function __construct(
        private UriVariablesExtractorInterface $uriVariablesExtractor,
        private ContextProviderInterface $contextProvider,
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private RedirectHandlerInterface $redirectionHandler,
        private GridRegistryInterface $gridRegistry,
        private GridViewBuilderInterface $gridViewBuilder,
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

        if ($operation->getFilterForm() !== null) {
            $filterForm = $this->formFactory->create($operation->getFilterForm(), [], $operation->getFilterFormOptions());
            $filterForm->handleRequest($request);
        }
        $data = $this->provider->provide($operation, $uriVariables, $context);
        $form = null;

        if ($operation->getForm() !== null) {
            $form = $this->formFactory->create(CollectionType::class, $data, [
                'entry_type' => $operation->getForm(),
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
            $grid = $this->gridRegistry->get($operation->getGrid());
            $gridView = $this->gridViewBuilder->build($grid, $operation, $data);
        }

        if ($context['json'] ?? false) {
            $content = $this->serializer->serialize($data, 'json', $operation->getNormalizationContext());

            return new JsonResponse($content, Response::HTTP_OK, [], true);
        }

        return new Response($this->environment->render($operation->getTemplate(), [
            'grid' => $gridView ?? null,
            'resource' => $operation->getResource(),
            'operation' => $operation,
            'data' => $data,
            'filterForm' => $filterForm?->createView(),
            'form' => $form?->createView(),
        ]));
    }
}
