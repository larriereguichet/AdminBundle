<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Grid\ViewBuilder\GridViewBuilderInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
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
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private FormFactoryInterface $formFactory,
        private GridViewBuilderInterface $gridBuilder,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResponseHandlerInterface $responseHandler,
    ) {
    }

    public function __invoke(Request $request, CollectionOperationInterface $operation): Response
    {
        $context = [];

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
                $this->processor->process($data, $operation);

                return $this->responseHandler->createRedirectResponse($operation, $data);
            }
        }

        if ($operation->getGrid() !== null) {
            $grid = $this->gridBuilder->build($operation->getGrid(), $operation, $data);
        }
        $event = new ResourceControllerEvent($operation, $request, $data);
        $this->eventDispatcher->dispatchEvents($event, ResourceControllerEvents::RESOURCE_CONTROLLER);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $this->responseHandler->createResponse($operation, $data, [
            'form' => $form ?? null,
            'filterForm' => $filterForm ?? null,
            'grid' => $grid ?? null,
        ]);
    }
}
