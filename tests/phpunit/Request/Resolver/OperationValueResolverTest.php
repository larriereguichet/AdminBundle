<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Resolver;

use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Request\Resolver\OperationValueResolver;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\Get;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\Operation;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\Tests\TestCase;
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
    #[DataProvider(methodName: 'supportTypes')]
    public function itResolvesRequestArguments(string $type): void
    {
        $request = new Request();

        $this->resourceContext
            ->expects(self::once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects(self::once())
            ->method('getOperation')
            ->willReturn(new Get())
        ;
        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', $type, false, false, null));
        $parameters = iterator_to_array($parameters);

        self::assertEquals(new Get(), $parameters[0]);
    }

    #[Test]
    #[DataProvider(methodName: 'notSupportedTypes')]
    public function itDoesNotResolveWrongType(string $type): void
    {
        $request = new Request(['test']);
        $this->resourceContext
            ->expects(self::once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;

        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', $type, false, false, null));
        $parameters = iterator_to_array($parameters);

        $this->assertCount(0, $parameters);
    }

    #[Test]
    public function itDoesNotResolveWithoutSupports(): void
    {
        $request = new Request(['test']);
        $this
            ->resourceContext
            ->expects(self::once())
            ->method('supports')
            ->with($request)
            ->willReturn(false)
        ;

        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', null, false, false, null));
        $parameters = iterator_to_array($parameters);

        $this->assertCount(0, $parameters);
    }

    public static function supportTypes(): iterable
    {
        yield [Get::class];
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
        $this->resourceContext = self::createMock(ResourceContextInterface::class);
        $this->resolver = new OperationValueResolver($this->resourceContext);
    }
}
