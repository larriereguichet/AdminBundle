<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Condition;

use LAG\AdminBundle\Condition\Matcher\ConditionMatcher;
use LAG\AdminBundle\Condition\Matcher\ConditionMatcherInterface;
use LAG\AdminBundle\Metadata\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class ConditionMatcherTest extends TestCase
{
    private ConditionMatcherInterface $conditionMatcher;
    private MockObject $authorizationChecker;

    #[Test]
    public function itMatchesConditions(): void
    {
        $data = new \stdClass();
        $data->id = 666;
        $data->name = 'Some thing';

        $property = new Text(condition: 'this.name === "Some thing" and workflow.can(this, "order") and is_granted("ROLE_TESTER")');

        $workflow = $this->createMock(WorkflowInterface::class);
        $workflow->expects($this->once())
            ->method('can')
            ->with($data, 'order')
            ->willReturn(true)
        ;

        $this->authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_TESTER')
            ->willReturn(true)
        ;

        $result = $this
            ->conditionMatcher
            ->matchCondition($property, $data, [
                'some' => 'context',
                'workflow' => $workflow,
            ])
        ;

        self::assertTrue($result);
    }

    protected function setUp(): void
    {
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->conditionMatcher = new ConditionMatcher(
            $this->authorizationChecker,
        );
    }
}
