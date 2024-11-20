<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\TextHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TextExtension extends AbstractExtension
{
    public function __construct(
        private readonly TextHelperInterface $helper,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('pluralize', $this->helper->pluralize(...)),
            new TwigFilter('lag_admin_rich_text', $this->helper->richText(...), ['is_safe' => ['html']]),
        ];
    }
}
