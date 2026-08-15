<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\State\Processor;

use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Session\FlashMessageHelperInterface;
use LAG\AdminBundle\State\Processor\ProcessorInterface;
use LAG\AdminBundle\State\Processor\WorkflowProcessor;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

final class WorkflowProcessorTest extends TestCase
{
    private MockObject $registry;
    private MockObject $processor;
    private MockObject $flashMessageHelper;
    private WorkflowProcessor $workflowProcessor;

    #[Test]
    public function itAppliesAnEnabledTransition(): void
    {
        $data = new \stdClass();
        $operation = new Update(workflow: 'order', workflowTransition: 'prepare');
        $workflow = $this->createMock(WorkflowInterface::class);

        $this->registry->method('has')->willReturn(true);
        $this->registry->method('get')->willReturn($workflow);
        $workflow->expects($this->once())->method('can')->with($data, 'prepare')->willReturn(true);
        $workflow->expects($this->once())->method('apply')->with($data, 'prepare');
        $this->flashMessageHelper->expects($this->never())->method('error');
        $this->processor->expects($this->once())->method('process');

        $this->workflowProcessor->process($data, $operation);
    }

    #[Test]
    public function itRefusesADisabledTransitionInsteadOfThrowing(): void
    {
        $data = new \stdClass();
        $operation = new Update(workflow: 'order', workflowTransition: 'deliver');
        $workflow = $this->createMock(WorkflowInterface::class);

        $this->registry->method('has')->willReturn(true);
        $this->registry->method('get')->willReturn($workflow);
        $workflow->expects($this->once())->method('can')->with($data, 'deliver')->willReturn(false);

        // Workflow::apply() would throw NotEnabledTransitionException, and nothing catches it on the way out
        $workflow->expects($this->never())->method('apply');
        $this->flashMessageHelper
            ->expects($this->once())
            ->method('error')
            ->with(WorkflowProcessor::TRANSITION_NOT_ENABLED_MESSAGE)
        ;
        // and the record must not be processed further either
        $this->processor->expects($this->never())->method('process');

        $this->workflowProcessor->process($data, $operation);
    }

    #[Test]
    public function itDelegatesWhenTheOperationHasNoWorkflow(): void
    {
        $data = new \stdClass();
        $operation = new Update();

        $this->registry->expects($this->never())->method('get');
        $this->flashMessageHelper->expects($this->never())->method('error');
        $this->processor->expects($this->once())->method('process');

        $this->workflowProcessor->process($data, $operation);
    }

    protected function setUp(): void
    {
        $this->registry = $this->createMock(Registry::class);
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->flashMessageHelper = $this->createMock(FlashMessageHelperInterface::class);
        $this->workflowProcessor = new WorkflowProcessor(
            $this->registry,
            $this->processor,
            $this->flashMessageHelper,
        );
    }
}
