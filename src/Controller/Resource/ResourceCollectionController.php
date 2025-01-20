<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\Registry\GridRegistryInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Request\Uri\UrlVariablesExtractorInterface;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResourceCollectionController
{
    public function __construct(
        private UrlVariablesExtractorInterface $uriVariablesExtractor,
        private ContextBuilderInterface $contextBuilder,
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private GridRegistryInterface $gridRegistry,
        private GridViewBuilderInterface $gridViewBuilder,
        private FormFactoryInterface $formFactory,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(Request $request, CollectionOperationInterface $operation): Response
    {
        $uriVariables = $this->uriVariablesExtractor->extractVariables($operation, $request);
        $context = $this->contextBuilder->buildContext($operation, $request);
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

                return $this->responseHandler->createRedirectResponse($operation, $data, $context);
            }
        }

        if ($operation->getGrid() !== null) {
            $grid = $this->gridRegistry->get($operation->getGrid());
            $gridView = $this->gridViewBuilder->build($operation, $grid, $data, $context);
        }
        $this->eventDispatcher->dispatchEvents(
            $event = new ResourceControllerEvent($operation, $request, $data, $context),
            ResourceControllerEvents::RESOURCE_CONTROLLER_PATTERN,
            $operation->getResource()->getApplication(),
            $operation->getResource()->getName(),
            $operation->getName(),
        );

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $this->responseHandler->createCollectionResponse(
            request: $request,
            operation: $operation,
            data: $data,
            form: $form,
            filterForm: $filterForm,
            grid: $gridView ?? null,
        );
    }
}
