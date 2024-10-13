<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Workflow;

interface WorkflowSubjectInterface
{
    public function getWorkflow(): ?string;
}
