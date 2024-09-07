<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionChecker;
use LAG\AdminBundle\Security\PermissionChecker\PropertyPermissionCheckerInterface;
use LAG\AdminBundle\Security\Voter\OperationPermissionVoter;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SecurityServicesTest extends TestCase
{
    use ContainerTestTrait;

    #[Test]
    public function servicesExists(): void
    {
        self::assertService(OperationPermissionVoter::class);

        self::assertService(PropertyPermissionCheckerInterface::class);
        self::assertNoService(PropertyPermissionChecker::class);
    }

}
