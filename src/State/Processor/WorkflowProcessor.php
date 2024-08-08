<?php

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use Symfony\Component\Workflow\Registry;

final readonly class WorkflowProcessor implements ProcessorInterface
{
    public function __construct(
        private Registry $workflowRegistry,
        private ProcessorInterface $processor,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation->getWorkflow() !== null && $this->workflowRegistry->has($data, $operation->getWorkflow())) {
            $workflow = $this->workflowRegistry->get($data, $operation->getWorkflow());
            $workflow->apply($data, $operation->getWorkflowTransition());
        }

        $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
