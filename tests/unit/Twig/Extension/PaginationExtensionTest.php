<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Twig\Extension\PaginationExtension;
use LAG\AdminBundle\View\Helper\PaginationHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

final class PaginationExtensionTest extends TestCase
{
    #[Test]
    public function itDefinesTwigFunctions(): void
    {
        $extension = new PaginationExtension();

        self::assertEquals([
            new TwigFunction('lag_admin_is_pager', [PaginationHelper::class, 'isPager']),
        ], $extension->getFunctions());
    }
}
