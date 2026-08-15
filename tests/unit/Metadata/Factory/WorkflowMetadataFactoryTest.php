<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Exception\Operation\InvalidWorkflowTransitionException;
use LAG\AdminBundle\Exception\Operation\MissingWorkflowException;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\WorkflowMetadataFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;

final class WorkflowMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itAcceptsATransitionTheWorkflowDefines(): void
    {
        $factory = $this->createFactory(new Update(name: 'prepare', workflow: 'order', workflowTransition: 'prepare'));

        $resource = $factory->createMetadata('order');

        self::assertCount(1, $resource->getOperations());
    }

    #[Test]
    public function itRejectsATransitionTheWorkflowDoesNotDefine(): void
    {
        $factory = $this->createFactory(new Update(name: 'deliver', workflow: 'order', workflowTransition: 'deliver'));

        $this->expectException(InvalidWorkflowTransitionException::class);
        // the message has to name the available transitions, that is the whole point of failing here
        $this->expectExceptionMessage('Available transitions: "create", "prepare", "cancel".');

        $factory->createMetadata('order');
    }

    #[Test]
    public function itRejectsAWorkflowThatIsNotRegistered(): void
    {
        $factory = $this->createFactory(new Update(name: 'pay', workflow: 'invoice', workflowTransition: 'pay'));

        $this->expectException(MissingWorkflowException::class);
        $this->expectExceptionMessage('Available workflows: "order".');

        $factory->createMetadata('order');
    }

    #[Test]
    public function itIgnoresAnOperationWithoutWorkflow(): void
    {
        $factory = $this->createFactory(new Update(name: 'update'));

        $resource = $factory->createMetadata('order');

        self::assertCount(1, $resource->getOperations());
    }

    private function createFactory(Update $operation): WorkflowMetadataFactory
    {
        $resource = new Resource(
            shortName: 'order',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [$operation],
        );
        $operation->setResource($resource);

        $metadataFactory = $this->createStub(ResourceMetadataFactoryInterface::class);
        $metadataFactory->method('createMetadata')->willReturn($resource);

        $definition = new Definition(
            ['cart', 'order', 'fulfilled', 'cancelled'],
            [
                new Transition('create', 'cart', 'order'),
                new Transition('prepare', 'order', 'fulfilled'),
                new Transition('cancel', 'order', 'cancelled'),
            ],
        );

        return new WorkflowMetadataFactory(
            $metadataFactory,
            new ServiceLocator(['order' => static fn (): Workflow => new Workflow($definition, name: 'order')]),
        );
    }
}
