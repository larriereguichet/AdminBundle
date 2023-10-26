<?php

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Metadata\Factory\ResourceFactoryValidationDecorator;
use LAG\AdminBundle\Metadata\GetCollection;
use LAG\AdminBundle\Metadata\Operation;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Validation\Constraint\AdminValid;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResourceFactoryValidationDecoratorTest extends TestCase
{
    private ResourceFactoryValidationDecorator $decorator;
    private MockObject $decorated;
    private MockObject $validator;

    public function testCreate(): void
    {
        $resource = new AdminResource(name: 'my_resource', operations: [
            new GetCollection(),
        ]);

        $this
            ->decorated
            ->expects($this->once())
            ->method('create')
            ->with($resource)
            ->willReturn($resource)
        ;

        $constrainViolations = $this->createMock(ConstraintViolationListInterface::class);
        $constrainViolations
            ->method('count')
            ->willReturn(0)
        ;

        $this
            ->validator
            ->expects($this->exactly(2))
            ->method('validate')
            ->willReturnCallback(function (mixed $value, array $constraints) use ($constrainViolations) {
                if ($value instanceof AdminResource) {
                    $this->assertEquals([new AdminValid(), new Valid()], $constraints);

                    return $constrainViolations;
                }

                if ($value instanceof Operation) {
                    $this->assertEquals([new Valid()], $constraints);

                    return $constrainViolations;
                }

                $this->fail();
            })
        ;

        $this->decorator->create($resource);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(ResourceFactoryInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->decorator = new ResourceFactoryValidationDecorator(
            $this->validator,
            $this->decorated,
        );
    }
}
