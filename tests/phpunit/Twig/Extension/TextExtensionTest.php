<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Twig\Extension;

use LAG\AdminBundle\Tests\TestCase;
use LAG\AdminBundle\Twig\Extension\TextExtension;
use LAG\AdminBundle\View\Helper\TextHelper;
use PHPUnit\Framework\Attributes\Test;
use Twig\TwigFilter;

final class TextExtensionTest extends TestCase
{
    #[Test]
    public function itReturnsTwigFilters(): void
    {
        $extension = new TextExtension();

        self::assertEquals([
            new TwigFilter('lag_admin_pluralize', [TextHelper::class, 'pluralize']),
            new TwigFilter('lag_admin_rich_text', [TextHelper::class, 'richText'], ['is_safe' => ['html']]),
        ], $extension->getFilters());
    }
}
