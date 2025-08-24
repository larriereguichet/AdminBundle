<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Initializer;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Action;
use LAG\AdminBundle\Metadata\OperationInterface;

use function Symfony\Component\String\u;

final readonly class ActionInitializer implements ActionInitializerInterface
{
    public function initializeAction(OperationInterface $operation, Action $action): Action
    {
        $resource = $operation->getResource();

        if ($resource === null) {
            throw new Exception('The resource should be initialized');
        }

        if ($action->getName() === null) {
            /** @var Action $action */
            $action = $action->withName($operation->getName());
        }

        if (!u($action->getOperation())->containsAny('.')) {
            $action = $action->withOperation($resource->getApplication().'.'.$resource->getName().'.'.$action->getOperation());
        }

        if ($action->getTranslationDomain() === null && $action->isTranslatable()) {
            /** @var Action $action */
            $action = $action->withTranslationDomain($resource->getTranslationDomain());
        }

        if ($action->getLabel() === null) {
            /** @var Action $action */
            $action = $action->withLabel(u($operation->getFullName())->title()->toString());

            if ($resource->getTranslationDomain()) {
                $label = u($resource->getTranslationPattern() ?? '{application}.{resource}.{message}')
                    ->replace('{application}', $resource->getApplication())
                    ->replace('{resource}', $resource->getName())
                    ->replace('{operation}', $operation->getName())
                    ->replace('{message}', $action->getName())
                    ->toString()
                ;
                /** @var Action $action */
                $action = $action->withLabel($label);
            }
        }

        return $action;
    }
}
