<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\Factory\GridFactoryInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridBuilderInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class IndexResources
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder,
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private FormFactoryInterface $formFactory,
        private GridFactoryInterface $gridFactory,
        private GridBuilderInterface $gridBuilder,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(Request $request, CollectionOperationInterface $operation, ?GridInterface $grid = null): Response
    {
        $context = $this->contextBuilder->buildContext($request, $operation, $grid);
        $form = null;
        $filterForm = null;

        if ($operation->getFilterForm() !== null) {
            $filterForm = $this->formFactory->create($operation->getFilterForm(), null, $operation->getFilterFormOptions());
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                $context['filters'] = $filterForm->getData();
            }
        }
        $data = $this->provider->provide($operation, [], $context);

        if ($operation->getForm() !== null) {
            $form = $this->formFactory->create(CollectionType::class, $data, [
                'entry_type' => $operation->getForm(),
                'entry_options' => $operation->getFormOptions(),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $this->processor->process($data, $operation, [], $context);

                return $this->responseHandler->createRedirectResponse($request, $operation, $data);
            }
        }

        if ($operation->getGrid() !== null) {
            $grid = $this->gridFactory->create($operation->getGrid(), $operation);
            $gridView = $this->gridBuilder->build($grid, $operation, $data, $context);
        }
        $event = new ResourceControllerEvent($operation, $request, $data);
        $this->eventDispatcher->dispatchEvents($event, ResourceControllerEvents::RESOURCE_CONTROLLER);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $this->responseHandler->createResponse($request, $operation, $data, [
            'form' => $form?->createView(),
            'filterForm' => $filterForm?->createView(),
            'grid' => $gridView ?? null,
        ]);
    }
}
