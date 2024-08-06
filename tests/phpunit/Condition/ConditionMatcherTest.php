<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Condition;

use LAG\AdminBundle\Condition\ConditionMatcher;
use LAG\AdminBundle\Condition\ConditionMatcherInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

final class ConditionMatcherTest extends TestCase
{
    private ConditionMatcherInterface $conditionMatcher;
    private MockObject $workflowRegistry;

    #[Test]
    public function itMatchesConditions(): void
    {
        $data = new \stdClass();
        $data->id = 666;
        $data->name = 'Some thing';

        $workflow = self::createMock(WorkflowInterface::class);
        $workflow->expects(self::once())
            ->method('can')
            ->with($data, 'order')
            ->willReturn(true)
        ;

        $this->workflowRegistry
            ->expects(self::once())
            ->method('get')
            ->with($data, 'my_workflow')
            ->willReturn($workflow)
        ;

        $result = $this
            ->conditionMatcher
            ->matchCondition($data, 'this.name === "Some thing" and workflow.can(this, "order")', [], 'my_workflow')
        ;

        self::assertTrue($result);
    }

    protected function setUp(): void
    {
        $this->workflowRegistry = self::createMock(Registry::class);
        $this->conditionMatcher = new ConditionMatcher($this->workflowRegistry);
    }
}
