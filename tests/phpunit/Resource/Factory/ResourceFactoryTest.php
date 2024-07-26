<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Factory;

use LAG\AdminBundle\Event\ResourceEvent;
use LAG\AdminBundle\Event\ResourceEvents;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\InvalidResourceException;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ResourceFactoryTest extends TestCase
{
    private ResourceFactoryInterface $resourceFactory;
    private MockObject $operationFactory;
    private MockObject $validator;

    #[Test]
    public function itCreatesAResourceFromADefinition(): void
    {
        $operationDefinition = new Get(name: 'my_operation');
        $definition = new Resource(
            name: 'my_resource',
            operations: [$operationDefinition],
        );

        $this->operationFactory
            ->expects(self::once())
            ->method('create')
            ->with($operationDefinition->withResource($definition))
            ->willReturn($operationDefinition)
        ;
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with($definition)
        ;

        $resource = $this->resourceFactory->create($definition);

        self::assertEquals($definition->getName(), $resource->getName());
    }

    #[Test]
    public function itDoesNotCreateInvalidResource(): void
    {
        $operationDefinition = new Get(name: 'my_operation');
        $definition = new Resource(
            name: 'my_resource',
            operations: [$operationDefinition],
        );
        $constraintViolationList = self::createMock(ConstraintViolationList::class);
        $constraintViolationList->expects(self::atLeastOnce())
            ->method('count')
            ->willReturn(1)
        ;

        $this->operationFactory
            ->expects(self::once())
            ->method('create')
            ->with($operationDefinition->withResource($definition))
            ->willReturn($operationDefinition)
        ;
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->with($definition)
            ->willReturn($constraintViolationList)
        ;

        self::expectExceptionObject(new InvalidResourceException($definition->getName(), $constraintViolationList));

        $this->resourceFactory->create($definition);
    }

    protected function setUp(): void
    {
        $this->operationFactory = self::createMock(OperationFactoryInterface::class);
        $this->validator = self::createMock(ValidatorInterface::class);
        $this->resourceFactory = new ResourceFactory(
            $this->operationFactory,
            $this->validator,
        );
    }
}
