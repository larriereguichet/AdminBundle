<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Request\Resolver;

use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Metadata\Attribute\Create;
use LAG\AdminBundle\Metadata\Attribute\Delete;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Request\ValueResolver\OperationValueResolver;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Tests\Unit\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class OperationValueResolverTest extends TestCase
{
    private OperationValueResolver $resolver;
    private MockObject $resourceContext;

    #[Test]
    #[DataProvider('supportTypes')]
    public function itResolvesRequestArguments(string $type): void
    {
        $request = new Request();

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('getOperation')
            ->willReturn(new Show())
        ;
        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', $type, false, false, null));
        $parameters = iterator_to_array($parameters);

        self::assertEquals(new Show(), $parameters[0]);
    }

    #[Test]
    #[DataProvider('notSupportedTypes')]
    public function itDoesNotResolveWrongType(string $type): void
    {
        $request = new Request(['test']);
        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;

        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', $type, false, false, null));
        $parameters = iterator_to_array($parameters);

        $this->assertCount(0, $parameters);
    }

    #[Test]
    public function itNotResolveArgumentsWithoutRequestParameters(): void
    {
        $request = new Request();

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(false)
        ;
        $this->resourceContext
            ->expects($this->never())
            ->method('getOperation')
        ;
        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', null, false, false, null));
        iterator_to_array($parameters);
    }

    public static function supportTypes(): iterable
    {
        yield [Show::class];
        yield [Create::class];
        yield [Update::class];
        yield [Delete::class];
        yield [Index::class];
        yield [OperationInterface::class];
        yield [CollectionOperationInterface::class];
    }

    public static function notSupportedTypes(): iterable
    {
        yield ['string'];
        yield ['int'];
        yield ['bool'];
        yield [Resource::class];
        yield [LAGAdminBundle::class];
    }

    protected function setUp(): void
    {
        $this->resourceContext = $this->createMock(ResourceContextInterface::class);
        $this->resolver = new OperationValueResolver($this->resourceContext);
    }
}
