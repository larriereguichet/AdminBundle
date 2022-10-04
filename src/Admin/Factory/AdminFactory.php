<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\Factory;

use LAG\AdminBundle\Event\AdminEvent;
use LAG\AdminBundle\Exception\Validation\InvalidAdminException;
use LAG\AdminBundle\Metadata\Admin;
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

    public function create(Admin $resource): Admin
    {
        $event = new AdminEvent($resource);
        $this->eventDispatcher->dispatch($event, AdminEvent::ADMIN_CREATE);
        $resource = $event->getAdmin();
        $errors = $this->validator->validate($resource, [new AdminValid(), new Valid()]);

        if ($errors->count() > 0) {
            throw new InvalidAdminException($resource->getName(), $errors);
        }
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            $operations[] = $this->operationFactory->create($operation);
        }
        $resource = $resource->withOperations($operations);

        $event = new AdminEvent($resource);
        $this->eventDispatcher->dispatch($event, AdminEvent::ADMIN_CREATED);

        return $event->getAdmin();
    }
}
