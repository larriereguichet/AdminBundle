<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Factory;

use LAG\AdminBundle\Exception\InvalidResourceException;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Factory\ValidationResourceFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidationResourceFactoryTest extends TestCase
{
    private ValidationResourceFactory $validationResourceFactory;
    private MockObject $validator;
    private MockObject $resourceFactory;

    #[Test]
    public function itValidatesAResource(): void
    {
        $expectedResource = new Resource(name: 'some_resource');
        $errors = self::createMock(ConstraintViolationListInterface::class);

        $this->resourceFactory
            ->expects(self::once())
            ->method('create')
            ->with('some_resource')
            ->willReturn($expectedResource)
        ;
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->willReturn($errors)
        ;
        $errors->expects(self::once())
            ->method('count')
            ->willReturn(0)
        ;

        $resource = $this->validationResourceFactory->create('some_resource');

        self::assertEquals($expectedResource, $resource);
    }

    #[Test]
    public function itDoesNotReturnAnInvalidResource(): void
    {
        $expectedResource = new Resource(name: 'some_resource', application: 'some_application');
        $errors = self::createMock(ConstraintViolationListInterface::class);

        $this->resourceFactory
            ->expects(self::once())
            ->method('create')
            ->with('some_resource')
            ->willReturn($expectedResource)
        ;
        $this->validator
            ->expects(self::once())
            ->method('validate')
            ->willReturn($errors)
        ;
        $errors->expects(self::once())
            ->method('count')
            ->willReturn(1)
        ;
        self::expectExceptionObject(new InvalidResourceException(
            resourceName: 'some_application.some_resource',
            errors: $errors,
        ));

        $this->validationResourceFactory->create('some_resource');
    }

    protected function setUp(): void
    {
        $this->validator = self::createMock(ValidatorInterface::class);
        $this->resourceFactory = self::createMock(ResourceFactoryInterface::class);
        $this->validationResourceFactory = new ValidationResourceFactory(
            $this->resourceFactory,
            $this->validator,
        );
    }
}
