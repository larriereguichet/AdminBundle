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
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects(self::once())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn('my_application')
        ;
        $this->applicationFactory
            ->expects(self::once())
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
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects(self::once())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn(null)
        ;
        $this->applicationFactory
            ->expects(self::never())
            ->method('create')
        ;

        self::expectExceptionObject(new Exception('The current request is not supported by any application'));

        $this->applicationContext->getApplication();
    }

    #[Test]
    public function itChecksApplication(): void
    {
        $request = new Request();

        $this->requestStack
            ->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request)
        ;
        $this->parametersExtractor
            ->expects(self::once())
            ->method('getApplicationName')
            ->with($request)
            ->willReturn(null)
        ;

        self::assertFalse($this->applicationContext->hasApplication());
    }

    protected function setUp(): void
    {
        $this->requestStack = self::createMock(RequestStack::class);
        $this->parametersExtractor = self::createMock(ParametersExtractorInterface::class);
        $this->applicationFactory = self::createMock(ApplicationFactoryInterface::class);
        $this->applicationContext = new ApplicationContext(
            $this->requestStack,
            $this->parametersExtractor,
            $this->applicationFactory,
        );
    }
}
