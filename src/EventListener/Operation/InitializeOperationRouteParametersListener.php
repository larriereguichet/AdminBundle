<?php

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use function Symfony\Component\String\u;

final readonly class InitializeOperationRouteParametersListener
{
    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();

        if ($operation->getRouteParameters() !== null) {
            return;
        }
        $identifiers = $operation->getIdentifiers();

        if (empty($identifiers)) {
            $identifiers = $operation->getResource()->getIdentifiers();
        }

        if ($identifiers !== null && $operation->getPath() !== null) {
            $path = u($operation->getPath());

            if ($path->containsAny('{') && $path->containsAny('}')) {
                $parameters = [];

                foreach ($identifiers as $identifier => $getter) {
                    if (is_numeric($identifier)) {
                        $identifier = $getter;
                    }
                    $parameters[$identifier] = $getter;
                }
                $operation = $operation->withRouteParameters($parameters);
            }
        }

        if ($operation->getRouteParameters() === null) {
            $operation = $operation->withRouteParameters([]);
        }

        $event->setOperation($operation);
    }
}
