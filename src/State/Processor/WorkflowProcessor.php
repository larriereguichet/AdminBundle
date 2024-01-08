<?php

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Metadata\OperationInterface;
use Symfony\Component\Workflow\WorkflowInterface;

readonly class WorkflowProcessor implements ProcessorInterface
{
    public function __construct(
        /** @var iterable<WorkflowInterface> $workflows */
        private iterable $workflows,
        private ProcessorInterface $processor,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        $workflow = $this->getWorkflow($operation->getWorkflow());

        if ($workflow !== null && $operation->getWorkflowTransition()) {
            $workflow->apply($data, $operation->getWorkflowTransition());
        }

        $this->processor->process($data, $operation, $uriVariables, $context);
    }

    private function getWorkflow(?string $workflowName): ?WorkflowInterface
    {
        if ($workflowName === null) {
            return null;
        }

        foreach ($this->workflows as $workflow) {
            if ($workflow->getName() === $workflowName) {
                return $workflow;
            }
        }

        return null;
    }
}
