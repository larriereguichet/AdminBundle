<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\EventListener;

use LAG\AdminBundle\Event\Events\OperationCreateEvent;
use LAG\AdminBundle\Filter\Factory\FilterFactoryInterface;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;

class OperationCreateListener
{
    public function __construct(
        private FilterFactoryInterface $filterFactory,
    ) {
    }

    public function __invoke(OperationCreateEvent $event): void
    {
        /** @var CollectionOperationInterface|OperationInterface $operation */
        $operation = $event->getOperation();

        if (!$this->supports($operation)) {
            return;
        }
        $filters = [];

        foreach ($operation->getProperties() ?? [] as $property) {
            $filter = $this
                ->filterFactory
                ->createFromProperty($property)
            ;

            if (in_array($filter->getName(), $operation->getIdentifiers())) {
                $filter = $filter->withComparator('=');
            }
            $filters[] = $filter;
        }
        $event->setOperation($operation->withFilters($filters));
    }

    private function supports(OperationInterface $operation): bool
    {
        if (!$operation instanceof CollectionOperationInterface) {
            return false;
        }

        if (count($operation->getProperties()) === 0) {
            return false;
        }

        if ($operation->getFilters() !== null) {
            return false;
        }

        return true;
    }
}
