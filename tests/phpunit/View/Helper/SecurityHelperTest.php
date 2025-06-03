<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\View\Helper;

use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Show;
use LAG\AdminBundle\Resource\Factory\OperationFactoryInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use LAG\AdminBundle\View\Helper\SecurityHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class SecurityHelperTest extends TestCase
{
    private SecurityHelper $helper;
    private MockObject $operationFactory;
    private MockObject $security;

    #[Test]
    public function itChecksOperationPermissions(): void
    {
        $operation = new Show(shortName: 'my_operation');

        $this->operationFactory
            ->expects(self::once())
            ->method('create')
            ->with('my_resource.my_operation')
            ->willReturn($operation)
        ;

        $this->security
            ->expects(self::once())
            ->method('isGranted')
            ->with(OperationPermissionVoter::RESOURCE_ACCESS, $operation)
        ;

        $this->helper->isOperationAllowed('my_resource.my_operation');
    }

    protected function setUp(): void
    {
        $this->operationFactory = self::createMock(OperationFactoryInterface::class);
        $this->security = self::createMock(Security::class);
        $this->helper = new SecurityHelper(
            $this->operationFactory,
            $this->security,
        );
    }
}
