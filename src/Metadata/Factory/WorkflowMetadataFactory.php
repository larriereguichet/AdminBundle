<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Exception\Operation\InvalidWorkflowTransitionException;
use LAG\AdminBundle\Exception\Operation\MissingWorkflowException;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Checks that the workflow and the transition an operation declares actually exist.
 *
 * Nothing used to report a transition missing from its workflow: the operation was routed, its links rendered, and
 * Workflow::can() answered false without throwing, so the buttons never showed and the pages never worked. That
 * silence lets an admin configuration and a workflow definition drift apart for months. The check runs while the
 * resource metadata is built, so a mismatch surfaces on the first request after a cache clear.
 */
final readonly class WorkflowMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceMetadataFactoryInterface $metadataFactory,
        private ContainerInterface $workflowLocator,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $resource = $this->metadataFactory->createMetadata($resourceName);

        foreach ($resource->getOperations() as $operation) {
            $workflowName = $operation->getWorkflow();

            if ($workflowName === null) {
                continue;
            }

            if (!$this->workflowLocator->has($workflowName)) {
                throw new MissingWorkflowException($operation->getName(), $workflowName, $this->getWorkflowNames());
            }
            $workflow = $this->workflowLocator->get($workflowName);
            \assert($workflow instanceof WorkflowInterface);

            $transitions = array_map(
                static fn ($transition): string => $transition->getName(),
                $workflow->getDefinition()->getTransitions(),
            );

            if (!\in_array($operation->getWorkflowTransition(), $transitions, true)) {
                throw new InvalidWorkflowTransitionException(
                    $operation->getName(),
                    $workflowName,
                    $operation->getWorkflowTransition(),
                    array_values(array_unique($transitions)),
                );
            }
        }

        return $resource;
    }

    /** @return string[] */
    private function getWorkflowNames(): array
    {
        if (!method_exists($this->workflowLocator, 'getProvidedServices')) {
            return [];
        }

        return array_keys($this->workflowLocator->getProvidedServices());
    }
}
