<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Workflow;

interface WorkflowAwareInterface
{
    public function getWorkflow(): ?string;

    public function getWorkflowTransition(): ?string;
}
