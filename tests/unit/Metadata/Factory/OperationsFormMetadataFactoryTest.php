<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Form\Type\Resource\DeleteType;
use LAG\AdminBundle\Form\Type\Resource\ResourceDataType;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\Factory\OperationsFormMetadataFactory;
use LAG\AdminBundle\Metadata\Factory\ResourceMetadataFactoryInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OperationsFormMetadataFactoryTest extends TestCase
{
    #[Test]
    public function itSetsDefaultFormForCreateOperation(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Create()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsFormMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $createOperation = current($result->getOperations());
        self::assertSame(ResourceDataType::class, $createOperation->getForm());
        self::assertArrayHasKey('data_class', $createOperation->getFormOptions());
        self::assertSame(\stdClass::class, $createOperation->getFormOptions()['data_class']);
    }

    #[Test]
    public function itSetsDefaultFormForUpdateOperation(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Update()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsFormMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $updateOperation = current($result->getOperations());
        self::assertSame(ResourceDataType::class, $updateOperation->getForm());
    }

    #[Test]
    public function itUsesResourceFormWhenOperationHasNone(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            form: 'App\Form\BookType',
            formOptions: ['my_option' => true],
            operations: [new Create()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsFormMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $createOperation = current($result->getOperations());
        self::assertSame('App\Form\BookType', $createOperation->getForm());
    }

    #[Test]
    public function itSetsResourceOptionOnDeleteOperation(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Delete()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsFormMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $deleteOperation = current($result->getOperations());
        self::assertSame(DeleteType::class, $deleteOperation->getForm());
        self::assertSame($resource, $deleteOperation->getFormOption('resource'));
    }

    #[Test]
    public function itDoesNotModifyShowOperation(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            operations: [new Show()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsFormMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $showOperation = current($result->getOperations());
        self::assertNull($showOperation->getForm());
        self::assertSame([], $showOperation->getFormOptions());
    }

    #[Test]
    public function itPropagatesTranslationDomainToFormOptions(): void
    {
        $resource = $this->buildResource(new Resource(
            shortName: 'book',
            application: 'admin',
            resourceClass: \stdClass::class,
            translationDomain: 'books',
            operations: [new Create()],
        ));
        $decorated = $this->createMock(ResourceMetadataFactoryInterface::class);
        $decorated
            ->expects($this->once())
            ->method('createMetadata')
            ->willReturn($resource)
        ;

        $factory = new OperationsFormMetadataFactory($decorated);
        $result = $factory->createMetadata('admin.book');

        $createOperation = current($result->getOperations());
        self::assertSame('books', $createOperation->getFormOption('translation_domain'));
    }

    private function buildResource(Resource $resource): ResourceMetadataInterface
    {
        $operations = [];

        foreach ($resource->getOperations() as $operation) {
            $operation->setResource($resource);
            $operations[] = $operation;
        }

        return $resource->withOperations($operations);
    }
}
