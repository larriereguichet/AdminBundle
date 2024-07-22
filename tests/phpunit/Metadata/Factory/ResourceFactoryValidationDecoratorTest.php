<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Resource\Factory\EventResourceFactory;
use LAG\AdminBundle\Resource\Factory\ResourceFactoryInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Operation;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Validation\Constraint\AdminValid;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResourceFactoryValidationDecoratorTest extends TestCase
{
    private EventResourceFactory $decorator;
    private MockObject $decorated;
    private MockObject $validator;

    public function testCreate(): void
    {
        $resource = new Resource(name: 'my_resource', operations: [
            new Index(),
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
                if ($value instanceof Resource) {
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
        $this->decorator = new EventResourceFactory(
            $this->validator,
            $this->decorated,
        );
    }
}
