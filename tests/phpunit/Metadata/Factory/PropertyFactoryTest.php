<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Exception\Validation\InvalidPropertyException;
use LAG\AdminBundle\Metadata\Factory\PropertyFactory;
use LAG\AdminBundle\Metadata\Property\StringProperty;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PropertyFactoryTest extends TestCase
{
    private PropertyFactory $factory;
    private MockObject $validator;

    public function testCreate(): void
    {
        $definition = new StringProperty(name: 'my_property');

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->with($definition, [new Valid()])
            ->willReturn(new ConstraintViolationList())
        ;

        $this->factory->create($definition);
    }

    public function testCreateInvalid(): void
    {
        $definition = new StringProperty(name: 'my_property');
        $violations = $this->createMock(ConstraintViolationList::class);

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->with($definition, [new Valid()])
            ->willReturn($violations)
        ;

        $violations
            ->expects($this->once())
            ->method('count')
            ->willReturn(1)
        ;

        $this->expectException(InvalidPropertyException::class);
        $this->factory->create($definition);
    }

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->factory = new PropertyFactory($this->validator);
    }
}
