<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Security\Voter;

use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Security\Voter\OperationVoter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class OperationPermissionVoterTest extends TestCase
{
    private OperationVoter $voter;
    private MockObject $security;

    #[Test]
    public function itChecksOperationPermissions(): void
    {
        $user = $this->createMock(UserInterface::class);
        $operation = new Update(permissions: ['ROLE_USER']);

        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_USER', $user)
            ->willReturn(true)
        ;

        $authorized = $this->voter->vote(
            new UsernamePasswordToken($user, 'admin', ['ROLE_USER']),
            $operation,
            [OperationVoter::OPERATION_ACCESS],
        );

        self::assertEquals(VoterInterface::ACCESS_GRANTED, $authorized);
    }

    #[Test]
    public function itAllowsOperationWithoutPermissions(): void
    {
        $user = $this->createMock(UserInterface::class);
        $operation = new Update(permissions: []);

        $this->security
            ->expects($this->never())
            ->method('isGranted')
        ;

        $authorized = $this->voter->vote(
            new UsernamePasswordToken($user, 'admin', ['ROLE_USER']),
            $operation,
            [OperationVoter::OPERATION_ACCESS],
        );

        self::assertEquals(VoterInterface::ACCESS_GRANTED, $authorized);
    }

    #[Test]
    public function itDoesNotChecksPermissionsOnInvalidAttribute(): void
    {
        $this->security
            ->expects($this->never())
            ->method('isGranted')
        ;
        $authorized = $this->voter->vote(
            new UsernamePasswordToken($this->createMock(UserInterface::class), 'admin', ['ROLE_USER']),
            new Update(permissions: ['ROLE_USER']),
            ['ROLE_ADMIN'],
        );

        self::assertEquals(VoterInterface::ACCESS_ABSTAIN, $authorized);
    }

    #[Test]
    public function itDoesNotChecksPermissionsOnInvalidSubject(): void
    {
        $this->security
            ->expects($this->never())
            ->method('isGranted')
        ;
        $authorized = $this->voter->vote(
            new UsernamePasswordToken($this->createMock(UserInterface::class), 'admin', ['ROLE_USER']),
            new \stdClass(),
            [OperationVoter::OPERATION_ACCESS],
        );

        self::assertEquals(VoterInterface::ACCESS_ABSTAIN, $authorized);
    }

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->voter = new OperationVoter($this->security);
    }
}
