<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Request\Resolver;

use LAG\AdminBundle\LAGAdminBundle;
use LAG\AdminBundle\Metadata\CollectionOperationInterface;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Request\Resolver\OperationValueResolver;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class OperationValueResolverTest extends TestCase
{
    private OperationValueResolver $resolver;
    private MockObject $parametersExtractor;
    private MockObject $operationContext;

    #[Test]
    #[DataProvider('supportTypes')]
    public function itResolvesRequestArguments(string $type): void
    {
        $request = new Request();

        $this->parametersExtractor
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(true)
        ;
        $this->operationContext
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
        $this->parametersExtractor
            ->expects($this->once())
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
        $this->parametersExtractor
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(false)
        ;

        $parameters = $this->resolver->resolve($request, new ArgumentMetadata('test', null, false, false, null));
        $parameters = iterator_to_array($parameters);

        $this->assertCount(0, $parameters);
    }

    #[Test]
    public function itNotResolveArgumentsWithoutRequestParameters(): void
    {
        $request = new Request();

        $this->parametersExtractor
            ->expects($this->once())
            ->method('supports')
            ->with($request)
            ->willReturn(false)
        ;
        $this->operationContext
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
        $this->parametersExtractor = $this->createMock(ParametersExtractorInterface::class);
        $this->operationContext = $this->createMock(OperationContextInterface::class);
        $this->resolver = new OperationValueResolver(
            $this->parametersExtractor,
            $this->operationContext,
        );
    }
}
