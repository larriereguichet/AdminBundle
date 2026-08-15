<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Controller\Resource;

use LAG\AdminBundle\Event\ResourceControllerEvent;
use LAG\AdminBundle\Event\ResourceControllerEvents;
use LAG\AdminBundle\EventDispatcher\ResourceEventDispatcherInterface;
use LAG\AdminBundle\Form\Type\Resource\BatchType;
use LAG\AdminBundle\Grid\ViewBuilder\GridBuilderInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GridInterface;
use LAG\AdminBundle\Request\ContextBuilder\ContextBuilderInterface;
use LAG\AdminBundle\Response\Handler\ResponseHandlerInterface;
use LAG\AdminBundle\Security\Voter\OperationVoter;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final readonly class IndexResources
{
    public function __construct(
        private ContextBuilderInterface $contextBuilder,
        private ProviderInterface $provider,
        private ProcessorInterface $processor,
        private FormFactoryInterface $formFactory,
        private GridBuilderInterface $gridBuilder,
        private ResourceEventDispatcherInterface $eventDispatcher,
        private ResponseHandlerInterface $responseHandler,
        private Security $security,
    ) {
    }

    public function __invoke(Request $request, CollectionOperationInterface $operation, ?GridInterface $grid = null): Response
    {
        $context = $this->contextBuilder->buildContext($request, $operation, $grid);
        $form = null;
        $filterForm = null;
        $batchForm = null;

        if ($operation->getFilterForm() !== null) {
            $filterForm = $this->formFactory->create($operation->getFilterForm(), null, $operation->getFilterFormOptions());
            $filterForm->handleRequest($request);

            if ($filterForm->isSubmitted() && $filterForm->isValid()) {
                $context['filters'] = $filterForm->getData();
            }
        }

        if ($operation->getBatchOperations() !== []) {
            $batchForm = $this->formFactory->create(BatchType::class, null, [
                'operations' => $operation->getBatchOperations(),
            ]);
            $batchForm->handleRequest($request);

            if ($batchForm->isSubmitted() && $batchForm->isValid()) {
                $targetOperationName = $batchForm->get('operation')->getData();
                $ids = $request->request->all()['batch_ids'] ?? [];
                $targetOperation = $operation->getResource()->getOperation($targetOperationName);
                $identifier = $operation->getResource()->getIdentifiers()[0] ?? 'id';

                // The batch operation is picked by the user in the submitted form: it has to be authorized on its
                // own, the access listener only granted the collection operation being displayed
                if (!$this->security->isGranted(OperationVoter::OPERATION_ACCESS, $targetOperation)) {
                    throw new AccessDeniedException(\sprintf(
                        'You are not allowed to run the batch operation "%s" on the resource "%s"',
                        $targetOperation->getName(),
                        $operation->getResource()->getName(),
                    ));
                }

                foreach (\is_array($ids) ? $ids : [$ids] as $id) {
                    $entity = $this->provider->provide($targetOperation, [$identifier => $id], $context);

                    if ($entity !== null) {
                        $this->processor->process($entity, $targetOperation, [$identifier => $id], $context);
                    }
                }

                return $this->responseHandler->createRedirectResponse($request, $operation, null);
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

        if ($grid !== null) {
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
            'batchForm' => $batchForm?->createView(),
            'grid' => $gridView ?? null,
        ]);
    }
}
