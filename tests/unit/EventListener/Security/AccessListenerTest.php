<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\EventListener\Security;

use LAG\AdminBundle\EventListener\Security\AccessListener;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Security\Voter\OperationVoter;
use LAG\AdminBundle\Tests\Unit\DataProviderTestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class AccessListenerTest extends TestCase
{
    use DataProviderTestTrait;

    private AccessListener $listener;
    private MockObject $resourceContext;
    private MockObject $security;

    #[Test]
    #[DataProvider('operations')]
    public function itAllowsOperationAccess(OperationInterface $operation): void
    {
        $request = new Request();
        $event = new RequestEvent($this->createStub(KernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
        $operation = $operation->withRoles(['ROLE_ADMIN']);

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('getOperation')
            ->willReturn($operation)
        ;
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with(OperationVoter::OPERATION_ACCESS, $operation)
            ->willReturn(true)
        ;
        $this->listener->__invoke($event);
    }

    #[Test]
    #[DataProvider('operations')]
    public function itDeniesOperationAccess(OperationInterface $operation): void
    {
        $request = new Request();
        $event = new RequestEvent($this->createStub(KernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
        $operation = $operation->withRoles(['ROLE_ADMIN']);

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(true)
        ;
        $this->resourceContext
            ->expects($this->once())
            ->method('getOperation')
            ->willReturn($operation)
        ;
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with(OperationVoter::OPERATION_ACCESS, $operation)
            ->willReturn(false)
        ;
        $this->expectExceptionObject(new AccessDeniedException('You are not allowed to access to this resource'));
        $this->listener->__invoke($event);
    }

    #[Test]
    public function itDoesNotApplyWithoutOperation(): void
    {
        $request = new Request();
        $event = new RequestEvent($this->createStub(KernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);

        $this->resourceContext
            ->expects($this->once())
            ->method('hasOperation')
            ->willReturn(false)
        ;
        $this->resourceContext
            ->expects($this->never())
            ->method('getOperation')
        ;
        $this->security
            ->expects($this->never())
            ->method('isGranted')
        ;
        $this->listener->__invoke($event);
    }

    protected function setUp(): void
    {
        $this->resourceContext = $this->createMock(ResourceContextInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->listener = new AccessListener(
            $this->resourceContext,
            $this->security,
        );
    }
}
