<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Twig\Globals;

use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Metadata\Index;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Resource\Context\ApplicationContextInterface;
use LAG\AdminBundle\Resource\Context\OperationContextInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Twig\Globals\LAGAdminGlobal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LAGAdminGlobalTest extends TestCase
{
    private LAGAdminGlobal $adminContext;
    private MockObject $applicationContext;
    private MockObject $resourceContext;
    private MockObject $operationContext;

    #[Test]
    public function itReturnsTheCurrentApplication(): void
    {
        $expectedApplication = new Application(name: 'my_application');

        $this->applicationContext
            ->expects(self::once())
            ->method('hasApplication')
            ->willReturn(true)
        ;
        $this->applicationContext
            ->expects(self::once())
            ->method('getApplication')
            ->willReturn($expectedApplication)
        ;

        $application = $this->adminContext->getApplication();

        self::assertEquals($expectedApplication, $application);
    }

    #[Test]
    public function itReturnsTheCurrentResource(): void
    {
        $expectedResource = new Resource(name: 'my_resource');

        $this->resourceContext
            ->expects(self::once())
            ->method('hasResource')
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects(self::once())
            ->method('getResource')
            ->willReturn($expectedResource)
        ;

        $resource = $this->adminContext->getResource();

        self::assertEquals($expectedResource, $resource);
    }

    #[Test]
    public function itReturnsTheCurrentOperation(): void
    {
        $expectedOperation = new Index(shortName: 'my_operation');

        $this->operationContext
            ->expects(self::once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->operationContext
            ->expects(self::once())
            ->method('getOperation')
            ->willReturn($expectedOperation)
        ;

        $operation = $this->adminContext->getOperation();

        self::assertEquals($expectedOperation, $operation);
    }

    protected function setUp(): void
    {
        $this->applicationContext = self::createMock(ApplicationContextInterface::class);
        $this->resourceContext = self::createMock(ResourceContextInterface::class);
        $this->operationContext = self::createMock(OperationContextInterface::class);
        $this->adminContext = new LAGAdminGlobal(
            $this->applicationContext,
            $this->resourceContext,
            $this->operationContext,
        );
    }
}
