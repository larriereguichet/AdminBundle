<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\View\Helper;

use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Show;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use LAG\AdminBundle\View\Helper\SecurityHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class SecurityHelperTest extends TestCase
{
    private SecurityHelper $helper;
    private MockObject $registry;
    private MockObject $security;

    #[Test]
    public function itChecksOperationPermissions(): void
    {
        $operation = new Show(name: 'my_operation');
        $resource = new Resource(name: 'my_resource', operations: [$operation]);

        $this->registry
            ->expects(self::once())
            ->method('get')
            ->with('my_resource')
            ->willReturn($resource)
        ;

        $this->security
            ->expects(self::once())
            ->method('isGranted')
            ->with(OperationPermissionVoter::RESOURCE_ACCESS, $operation)
        ;

        $this->helper->isOperationAllowed('my_resource', 'my_operation');
    }

    protected function setUp(): void
    {
        $this->registry = self::createMock(ResourceRegistryInterface::class);
        $this->security = self::createMock(Security::class);
        $this->helper = new SecurityHelper(
            $this->registry,
            $this->security,
        );
    }
}
