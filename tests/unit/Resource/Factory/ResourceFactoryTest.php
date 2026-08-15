<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Resource\Factory;

use LAG\AdminBundle\Exception\InvalidResourceException;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ResourceFactoryTest extends TestCase
{
    private ResourceFactoryInterface $resourceFactory;
    private MockObject $metadataFactory;
    private MockObject $validator;

    #[Test]
    public function itCreatesAResourceFromADefinition(): void
    {
        $definition = new Resource(shortName: 'my_resource', application: 'my_application');

        $this->metadataFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->with('my_application.my_resource')
            ->willReturn($definition)
        ;
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList())
        ;

        $resource = $this->resourceFactory->create('my_application.my_resource');

        self::assertEquals($definition->getShortName(), $resource->getShortName());
    }

    #[Test]
    public function itDoesNotCreateInvalidResource(): void
    {
        $definition = new Resource(shortName: 'my_resource', application: 'my_application');
        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->expects($this->once())
            ->method('count')
            ->willReturn(1)
        ;

        $this->metadataFactory
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($definition)
        ;
        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn($errors)
        ;

        $this->expectException(InvalidResourceException::class);
        $this->resourceFactory->create('my_application.my_resource');
    }

    protected function setUp(): void
    {
        $this->metadataFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->resourceFactory = new ResourceFactory(
            $this->metadataFactory,
            $this->validator,
        );
    }
}
