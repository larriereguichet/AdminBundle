<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Twig\Extension\SecurityExtension;
use LAG\AdminBundle\View\Helper\SecurityHelper;
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
            new TwigFunction('lag_admin_operation_allowed', [SecurityHelper::class, 'isOperationAllowed']),
        ], $extension->getFunctions());
    }
}
