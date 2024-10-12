<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Condition\Matcher;

use LAG\AdminBundle\Condition\ConditionalInterface;
use LAG\AdminBundle\Workflow\WorkflowSubjectInterface;
use LAG\AdminBundle\Workflow\WorkflowTransitionSubjectInterface;

final readonly class WorkflowSubject implements WorkflowSubjectInterface, WorkflowTransitionSubjectInterface, ConditionalInterface
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