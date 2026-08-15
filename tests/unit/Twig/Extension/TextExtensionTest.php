<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Twig\Extension;

use LAG\AdminBundle\Tests\Unit\TestCase;
use LAG\AdminBundle\Twig\Extension\RichTextExtension;
use LAG\AdminBundle\Twig\Runtime\RichTextRuntime;
use PHPUnit\Framework\Attributes\Test;
use Twig\TwigFilter;

final class TextExtensionTest extends TestCase
{
    #[Test]
    public function itReturnsTwigFilters(): void
    {
        $extension = new RichTextExtension();

        self::assertEquals([
            new TwigFilter('lag_admin_rich_text', [RichTextRuntime::class, 'renderRichText'], ['is_safe' => ['html']]),
        ], $extension->getFilters());
    }
}
