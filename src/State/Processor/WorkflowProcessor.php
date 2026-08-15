<?php

declare(strict_types=1);

namespace LAG\AdminBundle\State\Processor;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Session\FlashMessageHelperInterface;
use Symfony\Component\Workflow\Registry;

final readonly class WorkflowProcessor implements ProcessorInterface
{
    public const string TRANSITION_NOT_ENABLED_MESSAGE = 'lag_admin.ui.transition_not_enabled';

    public function __construct(
        private Registry $workflowRegistry,
        private ProcessorInterface $processor,
        private FlashMessageHelperInterface $flashMessageHelper,
    ) {
    }

    public function process(mixed $data, OperationInterface $operation, array $urlVariables = [], array $context = []): void
    {
        if ($operation->getWorkflow() !== null && $this->workflowRegistry->has($data, $operation->getWorkflow())) {
            $workflow = $this->workflowRegistry->get($data, $operation->getWorkflow());
            $transition = (string) $operation->getWorkflowTransition();

            // A display condition describes the state when the page was rendered, not when the form was submitted:
            // between the two, someone else may have moved the record along. Applying an unreachable transition
            // throws, so refuse the whole processing and say so, rather than answering a 500
            if (!$workflow->can($data, $transition)) {
                $this->flashMessageHelper->error(self::TRANSITION_NOT_ENABLED_MESSAGE);

                return;
            }
            $workflow->apply($data, $transition);
        }

        $this->processor->process($data, $operation, $urlVariables, $context);
    }
}
