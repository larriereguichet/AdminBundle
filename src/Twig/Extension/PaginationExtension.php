<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\PaginationHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PaginationExtension extends AbstractExtension
{
    public function __construct(
        private readonly PaginationHelperInterface $helper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lag_admin_is_pager', $this->helper->isPager(...)),
        ];
    }
}
