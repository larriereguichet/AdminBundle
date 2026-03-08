<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Resource\Factory;

use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\TextFilter;
use LAG\AdminBundle\Metadata\Factory\ResourceFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceInitializerInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Tests\Unit\TestCase;
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
            shortName: 'my_resource',
            applicationName: 'my_application',
            operations: [$operationDefinition, $collectionOperationDefinition],
        );

        $this->definitionFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($definition)
        ;
        $this->resourceInitializer
            ->expects($this->once())
            ->method('initializeResource')
            ->with($definition)
            ->willReturn($definition->withShortName('my_resource'))
        ;
        $resource = $this->resourceFactory->create('my_resource');

        self::assertEquals($definition->getShortName(), $resource->getName());
    }

    protected function setUp(): void
    {
        $this->definitionFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $this->resourceInitializer = $this->createMock(ResourceInitializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->resourceFactory = new ResourceFactory(
            $this->definitionFactory,
            $this->resourceInitializer,
            $this->validator,
        );
    }
}
