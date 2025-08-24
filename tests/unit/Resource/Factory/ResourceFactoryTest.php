<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Factory;

use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\TextFilter;
use LAG\AdminBundle\Resource\Factory\DefinitionFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Initializer\ResourceInitializerInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ResourceFactoryTest extends TestCase
{
    private ResourceFactoryInterface $resourceFactory;
    private MockObject $definitionFactory;
    private MockObject $resourceInitializer;
    private MockObject $validator;

    #[Test]
    public function itCreatesAResourceFromADefinition(): void
    {
        $operationDefinition = new Show(name: 'my_operation');
        $collectionOperationDefinition = new Index(
            name: 'my_collection_operation',
            filters: [new TextFilter(name: 'my_filter')],
        );
        $definition = new Resource(
            name: 'my_resource',
            application: 'my_application',
            operations: [$operationDefinition, $collectionOperationDefinition],
        );

        $this->definitionFactory
            ->expects($this->once())
            ->method('createResourceDefinition')
            ->willReturn($definition)
        ;
        $this->resourceInitializer
            ->expects($this->once())
            ->method('initializeResource')
            ->with($definition)
            ->willReturn($definition->withName('my_resource'))
        ;
        $resource = $this->resourceFactory->create('my_resource');

        self::assertEquals($definition->getName(), $resource->getName());
    }

    protected function setUp(): void
    {
        $this->definitionFactory = $this->createMock(DefinitionFactoryInterface::class);
        $this->resourceInitializer = $this->createMock(ResourceInitializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->resourceFactory = new ResourceFactory(
            $this->definitionFactory,
            $this->resourceInitializer,
            $this->validator,
        );
    }
}
