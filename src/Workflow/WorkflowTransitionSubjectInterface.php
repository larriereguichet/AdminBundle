<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Workflow;

interface WorkflowTransitionSubjectInterface
{
    public function getWorkflowTransition(): ?string;
}
