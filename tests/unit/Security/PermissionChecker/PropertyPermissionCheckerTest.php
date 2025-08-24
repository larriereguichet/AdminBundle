<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Security\PermissionChecker;

use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionChecker;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class PropertyPermissionCheckerTest extends TestCase
{
    private PropertyPermissionChecker $permissionChecker;
    private MockObject $security;

    #[Test]
    public function itCheckPropertyPermissions(): void
    {
        $property = new Text(permissions: ['ROLE_USER', 'ROLE_ADMIN']);
        $user = new InMemoryUser(username: 'my_user', password: 'my_password', roles: ['ROLE_USER']);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_USER', $user)
            ->willReturn(true)
        ;
        $granted = $this->permissionChecker->isGranted($property);

        self::assertTrue($granted);
    }

    #[Test]
    public function itCheckNotAllowedPropertyPermissions(): void
    {
        $property = new Text(permissions: ['ROLE_USER']);
        $user = new InMemoryUser(username: 'my_user', password: 'my_password', roles: ['ROLE_NOT_ALLOWED']);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user)
        ;
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_USER', $user)
            ->willReturn(false)
        ;
        $granted = $this->permissionChecker->isGranted($property);

        self::assertFalse($granted);
    }

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->permissionChecker = new PropertyPermissionChecker($this->security);
    }
}
