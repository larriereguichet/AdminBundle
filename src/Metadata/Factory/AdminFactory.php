<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Event\Events\ResourceCreatedEvent;
use LAG\AdminBundle\Event\Events\ResourceCreateEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\Exception\Validation\InvalidAdminException;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Validation\Constraint\AdminValid;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminFactory implements AdminFactoryInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ValidatorInterface $validator,
        private OperationFactoryInterface $operationFactory,
    ) {
    }

    public function create(AdminResource $resource): AdminResource
    {
        $event = new ResourceCreateEvent($resource);
        $this->eventDispatcher->dispatch($event, ResourceEvents::ADMIN_CREATE);
        $resource = $event->getResource();
        $errors = $this->validator->validate($resource, [new AdminValid(), new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidAdminException($resource->getName(), $errors);
        }
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            $operations[] = $this->operationFactory->create($resource, $operation);
        }
        $resource = $resource->withOperations($operations);

        $event = new ResourceCreatedEvent($resource);
        $this->eventDispatcher->dispatch($event, ResourceEvents::ADMIN_CREATED);

        return $event->getResource();
    }
}
