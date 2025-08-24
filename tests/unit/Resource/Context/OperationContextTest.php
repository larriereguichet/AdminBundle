<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\OperationContext;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class OperationContextTest extends TestCase
{
    private OperationContext $operationContext;
    private MockObject $requestStack;
    private MockObject $parametersExtractor;
    private MockObject $operationFactory;

    #[Test]
    public function itReturnsTheCurrentOperation(): void
    {
        $request = new Request();
        $expectedResource = new Index(name: 'my_operation');

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects($this->once())
            ->method('getOperationName')
            ->with($request)
            ->willReturn('my_operation')
        ;
        $this->operationFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_operation')
            ->willReturn($expectedResource)
        ;

        $application = $this->operationContext->getOperation();

        self::assertEquals($expectedResource, $application);
    }

    #[Test]
    public function itDoesNotReturnAMissingOperation(): void
    {
        $request = new Request();

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects($this->once())
            ->method('getOperationName')
            ->with($request)
            ->willReturn(null)
        ;
        $this->operationFactory
            ->expects(self::never())
            ->method('create')
        ;

        self::expectExceptionObject(new Exception('The current request is not supported by any resource or operation'));

        $this->operationContext->getOperation();
    }

    #[Test]
    public function itChecksForCurrentOperation(): void
    {
        $request = new Request();

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects($this->once())
            ->method('getOperationName')
            ->with($request)
            ->willReturn(null)
        ;

        self::assertFalse($this->operationContext->hasOperation());
    }

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->parametersExtractor = $this->createMock(ParametersExtractorInterface::class);
        $this->operationFactory = $this->createMock(OperationFactoryInterface::class);
        $this->operationContext = new OperationContext(
            $this->requestStack,
            $this->parametersExtractor,
            $this->operationFactory,
        );
    }
}
