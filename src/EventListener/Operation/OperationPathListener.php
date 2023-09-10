<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\Events\OperationEvent;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\GetCollection;

use function Symfony\Component\String\u;

class OperationPathListener
{
    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();

        if (!$operation->getPath()) {
            $path = u('/');

            if ($operation instanceof CollectionOperationInterface && !$operation instanceof GetCollection) {
                $path = $path->append($operation->getName());
            }

            if (!$operation instanceof CollectionOperationInterface) {
                foreach ($operation->getIdentifiers() as $identifier) {
                    $path = $path
                        ->append('{')
                        ->append($identifier)
                        ->append('}')
                        ->append('/')
                    ;
                }

                $path = $path
                    ->append($operation->getName())
                ;
            }
            $event->setOperation($operation->withPath($path->lower()->toString()));
        }
    }
}
