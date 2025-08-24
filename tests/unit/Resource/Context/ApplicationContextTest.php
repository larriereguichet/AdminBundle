<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Context\ApplicationContext;
use LAG\AdminBundle\Resource\Factory\ApplicationFactoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ApplicationContextTest extends TestCase
{
    private ApplicationContext $applicationContext;
    private MockObject $requestStack;
    private MockObject $parametersExtractor;
    private MockObject $applicationFactory;

    #[Test]
    public function itReturnsTheCurrentApplication(): void
    {
        $request = new Request();
        $expectedApplication = new Application(name: 'my_application');

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects($this->once())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn('my_application')
        ;
        $this->applicationFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_application')
            ->willReturn($expectedApplication)
        ;

        $application = $this->applicationContext->getApplication();

        self::assertEquals($expectedApplication, $application);
    }

    #[Test]
    public function itDoesNotReturnAMissingApplication(): void
    {
        $request = new Request();

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects($this->once())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn(null)
        ;
        $this->applicationFactory
            ->expects($this->never())
            ->method('create')
        ;

        $this->expectExceptionObject(new Exception('The current request is not supported by any application'));

        $this->applicationContext->getApplication();
    }

    #[Test]
    public function itChecksApplication(): void
    {
        $request = new Request();

        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects($this->once())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn(null)
        ;

        self::assertFalse($this->applicationContext->hasApplication());
    }

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->parametersExtractor = $this->createMock(ParametersExtractorInterface::class);
        $this->applicationFactory = $this->createMock(ApplicationFactoryInterface::class);
        $this->applicationContext = new ApplicationContext(
            $this->requestStack,
            $this->parametersExtractor,
            $this->applicationFactory,
        );
    }
}
