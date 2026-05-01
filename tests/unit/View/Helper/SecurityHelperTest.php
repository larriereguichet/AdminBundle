<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\View\Helper;

use LAG\AdminBundle\Metadata\Attribute\Show;
use LAG\AdminBundle\Metadata\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Security\Voter\OperationVoter;
use LAG\AdminBundle\Twig\Runtime\SecurityRuntime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class SecurityHelperTest extends TestCase
{
    private SecurityRuntime $helper;
    private MockObject $operationFactory;
    private MockObject $security;

    #[Test]
    public function itChecksOperationPermissions(): void
    {
        $operation = new Show(name: 'my_operation');

        $this->operationFactory
            ->expects($this->once())
            ->method('create')
            ->with('my_resource.my_operation')
            ->willReturn($operation)
        ;

        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with(OperationVoter::OPERATION_ACCESS, $operation)
        ;

        $this->helper->isOperationAllowed('my_resource.my_operation');
    }

    protected function setUp(): void
    {
        $this->operationFactory = $this->createMock(OperationFactoryInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->helper = new SecurityRuntime(
            $this->operationFactory,
            $this->security,
        );
    }
}
