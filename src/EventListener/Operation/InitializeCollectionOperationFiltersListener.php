<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\EntityFilter;

final readonly class InitializeCollectionOperationFiltersListener
{
    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();

        if (!$operation instanceof CollectionOperationInterface) {
            return;
        }
        $filters = $operation->getFilters();

        foreach ($filters as $index => $filter) {
            $formOptions = $filter->getFormOptions();

            if ($filter instanceof EntityFilter) {
                if ($filter->isMultiple() && empty($formOptions['multiple'])) {
                    $formOptions['multiple'] = true;
                }

                if ($filter->getProperty() === null) {
                    $filter = $filter->withProperty($filter->getName());
                }
            }
            $filters[$index] = $filter->withFormOptions($formOptions);
        }

        $event->setOperation($operation->withFilters($filters));
    }
}
