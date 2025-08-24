<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Condition\Matcher;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Condition\Matcher\WorkflowConditionMatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

final class WorkflowConditionMatcherTest extends TestCase
{
    private WorkflowConditionMatcher $conditionMatcher;
    private MockObject $decoratedConditionMatcher;
    private MockObject $workflowRegistry;

    #[Test]
    public function itAddWorkflowContext(): void
    {
        $subject = new WorkflowSubject(workflow: 'my_workflow');
        $data = new \stdClass();

        $workflow = self::createMock(WorkflowInterface::class);

        $this->workflowRegistry
            ->expects(self::once())
            ->method('get')
            ->with($data, 'my_workflow')
            ->willReturn($workflow)
        ;
        $this->decoratedConditionMatcher
            ->expects(self::once())
            ->method('matchCondition')
            ->with($subject, $data, ['workflow' => $workflow])
            ->willReturn(true)
        ;

        $condition = $this->conditionMatcher->matchCondition($subject, $data);

        self::assertTrue($condition);
    }

    #[Test]
    public function itDoesNotOverrideAlreadySetWorkflow(): void
    {
        $subject = new WorkflowSubject(workflow: 'my_workflow');
        $data = new \stdClass();

        $this->workflowRegistry
            ->expects(self::never())
            ->method('get')
        ;
        $this->decoratedConditionMatcher
            ->expects(self::once())
            ->method('matchCondition')
            ->with($subject, $data, ['workflow' => 'some_workflow'])
            ->willReturn(true)
        ;

        $condition = $this->conditionMatcher->matchCondition($subject, $data, ['workflow' => 'some_workflow']);

        self::assertTrue($condition);
    }

    #[Test]
    public function itAddWorkflowTransitionContext(): void
    {
        $subject = new WorkflowSubject(workflowTransition: 'my_workflow_transition');
        $data = new \stdClass();

        $this->workflowRegistry
            ->expects(self::never())
            ->method('get')
        ;
        $this->decoratedConditionMatcher
            ->expects(self::once())
            ->method('matchCondition')
            ->with($subject, $data, ['workflow_transition' => 'my_workflow_transition'])
            ->willReturn(true)
        ;

        $condition = $this->conditionMatcher->matchCondition($subject, $data);

        self::assertTrue($condition);
    }

    protected function setUp(): void
    {
        $this->decoratedConditionMatcher = self::createMock(ConditionMatcherInterface::class);
        $this->workflowRegistry = self::createMock(Registry::class);
        $this->conditionMatcher = new WorkflowConditionMatcher(
            $this->decoratedConditionMatcher,
            $this->workflowRegistry,
        );
    }
}
