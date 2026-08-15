<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Condition\Matcher;

use LAG\AdminBundle\Condition\ConditionalInterface;
use LAG\AdminBundle\Workflow\WorkflowAwareInterface;

final readonly class WorkflowAware implements WorkflowAwareInterface, ConditionalInterface
{
    public function __construct(
        private ?string $condition = null,
        private ?string $workflow = null,
        private ?string $workflowTransition = null,
    ) {
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function getWorkflow(): ?string
    {
        return $this->workflow;
    }

    public function getWorkflowTransition(): ?string
    {
        return $this->workflowTransition;
    }
}
