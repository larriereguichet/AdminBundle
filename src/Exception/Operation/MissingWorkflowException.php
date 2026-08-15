<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Exception\Operation;

use LAG\AdminBundle\Exception\Exception;

final class MissingWorkflowException extends Exception
{
    /** @param string[] $availableWorkflows */
    public function __construct(string $operationName, string $workflow, array $availableWorkflows)
    {
        parent::__construct(\sprintf(
            'The operation "%s" declares the workflow "%s", which is not registered. Available workflows: %s.',
            $operationName,
            $workflow,
            $availableWorkflows === [] ? 'none' : '"'.implode('", "', $availableWorkflows).'"',
        ));
    }
}
