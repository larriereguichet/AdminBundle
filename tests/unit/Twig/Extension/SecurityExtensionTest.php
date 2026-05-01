<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Twig\Extension;

use LAG\AdminBundle\Twig\Extension\SecurityExtension;
use LAG\AdminBundle\Twig\Runtime\SecurityRuntime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class SecurityExtensionTest extends TestCase
{
    #[Test]
    public function itReturnsTwigFunctions(): void
    {
        $extension = new SecurityExtension();

        self::assertEquals([
            new TwigFunction('lag_admin_operation_allowed', [SecurityRuntime::class, 'isOperationAllowed']),
        ], $extension->getFunctions());
    }
}
