<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Exception\InvalidPropertyException;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Property\PropertyInterface;
use LAG\AdminBundle\Metadata\Property\Text;
use LAG\AdminBundle\Resource\Factory\PropertyFactory;
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
        $definition = new Text(name: 'my_property');
        $resource = new Resource(name: 'a_resource', translationDomain: 'my_domain', applicationName: 'app');
        $operation = (new Index(properties: [$definition]))->withResource($resource);

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturnCallback(function (PropertyInterface $data, array $constraints) use ($definition) {
                $this->assertEquals($data->getName(), $definition->getName());
                $this->assertEquals([new Valid()], $constraints);
            })
            ->willReturn(new ConstraintViolationList())
        ;

        /** @var PropertyInterface[] $properties */
        $properties = $this->factory->createCollection($operation);

        $this->assertCount(1, $properties);
        $this->assertArrayHasKey('my_property', $properties);
        $this->assertEquals('my_property', $properties['my_property']->getName());
        $this->assertEquals('app.a_resource.my_property', $properties['my_property']->getLabel());
        $this->assertEquals('my_domain', $properties['my_property']->getTranslationDomain());
    }

    public function testCreateWithTranslationPattern(): void
    {
        $definition = new Text(name: 'my_property');
        $resource = new Resource(
            name: 'a_resource',
            applicationName: 'app',
            translationDomain: 'my_domain',
            translationPattern: 'test.{resource}.{message}',
        );
        $operation = (new Index(properties: [$definition]))->withResource($resource);

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturnCallback(function (PropertyInterface $data, array $constraints) use ($definition) {
                $this->assertEquals($data->getName(), $definition->getName());
                $this->assertEquals([new Valid()], $constraints);
            })
            ->willReturn(new ConstraintViolationList())
        ;

        /** @var PropertyInterface[] $properties */
        $properties = $this->factory->createCollection($operation);

        $this->assertCount(1, $properties);
        $this->assertArrayHasKey('my_property', $properties);
        $this->assertEquals('my_property', $properties['my_property']->getName());
        $this->assertEquals('test.a_resource.my_property', $properties['my_property']->getLabel());
        $this->assertEquals('my_domain', $properties['my_property']->getTranslationDomain());
    }

    public function testCreateInvalid(): void
    {
        $definition = new Text(name: 'my_property');
        $resource = new Resource(applicationName: 'app', name: 'a_resource', translationDomain: 'my_domain');
        $operation = (new Index(properties: [$definition]))->withResource($resource);
        $violations = $this->createMock(ConstraintViolationList::class);

        $this
            ->validator
            ->expects($this->once())
            ->method('validate')
            ->willReturnCallback(function (PropertyInterface $data, array $constraints) use ($definition, $violations) {
                $this->assertEquals($data->getName(), $definition->getName());
                $this->assertEquals([new Valid()], $constraints);

                return $violations;
            })
            ->willReturn($violations)
        ;

        $violations
            ->expects($this->once())
            ->method('count')
            ->willReturn(1)
        ;

        $this->expectException(InvalidPropertyException::class);
        $this->factory->createCollection($operation);
    }

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->factory = new PropertyFactory($this->validator);
    }
}
