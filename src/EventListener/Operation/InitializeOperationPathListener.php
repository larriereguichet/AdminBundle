<?php

declare(strict_types=1);

namespace LAG\AdminBundle\EventListener\Operation;

use LAG\AdminBundle\Event\OperationEvent;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use Symfony\Component\String\Inflector\EnglishInflector;
use function Symfony\Component\String\u;

final readonly class InitializeOperationPathListener
{
    public function __invoke(OperationEvent $event): void
    {
        $operation = $event->getOperation();
        $resource = $operation->getResource();

        if ($operation->getPath() === null) {
            $path = u('');
            $inflector = new EnglishInflector();
            $prefix = $inflector->pluralize(u($operation->getResource()->getName())->lower()->toString())[0];

            if ($operation->getResource()->getPathPrefix()) {
                $prefix = $resource->getPathPrefix();
            }
            $path = $path->append($prefix)
                ->ensureStart('/')
            ;

            if ($operation instanceof CollectionOperationInterface && !$operation instanceof Index) {
                $path = $path->append('/', $operation->getName());
            }

            if (!$operation instanceof CollectionOperationInterface) {
                $path = $path->ensureEnd('/');

                foreach ($operation->getIdentifiers() ?? [] as $identifier) {
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
        } elseif ($resource->getPathPrefix() !== null) {
            $path = u($operation->getPath())
                ->prepend($resource->getPathPrefix())
            ;
            $event->setOperation($operation->withPath($path->lower()->toString()));
        }
    }
}
