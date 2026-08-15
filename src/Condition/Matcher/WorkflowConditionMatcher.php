<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Condition\Matcher;

use LAG\AdminBundle\Condition\ConditionalInterface;
use LAG\AdminBundle\Workflow\WorkflowAwareInterface;
use Symfony\Component\Workflow\Registry;

final readonly class WorkflowConditionMatcher implements ConditionMatcherInterface
{
    public function __construct(
        private ConditionMatcherInterface $conditionMatcher,
        private Registry $workflowRegistry,
    ) {
    }

    public function matchCondition(ConditionalInterface $subject, mixed $data, array $context = []): bool
    {
        if ($subject instanceof WorkflowAwareInterface) {
            if ($subject->getWorkflow() !== null && !isset($context['workflow'])) {
                $context['workflow'] = $this->workflowRegistry->get($data, $subject->getWorkflow());
            }
            if ($subject->getWorkflowTransition() !== null) {
                $context['workflow_transition'] = $subject->getWorkflowTransition();
            }
        }

        return $this->conditionMatcher->matchCondition($subject, $data, $context);
    }
}
