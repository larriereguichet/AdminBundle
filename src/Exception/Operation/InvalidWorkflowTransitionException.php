<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Operation;

use LAG\AdminBundle\Exception\Exception;

final class InvalidWorkflowTransitionException extends Exception
{
    /** @param string[] $availableTransitions */
    public function __construct(string $operationName, string $workflow, ?string $transition, array $availableTransitions)
    {
        parent::__construct(\sprintf(
            'The operation "%s" declares the transition "%s" on the workflow "%s", which does not define it. Available transitions: %s.',
            $operationName,
            $transition ?? '',
            $workflow,
            $availableTransitions === [] ? 'none' : '"'.implode('", "', $availableTransitions).'"',
        ));
    }
}
