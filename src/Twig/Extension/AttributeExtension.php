<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use LAG\AdminBundle\View\Helper\AttributeHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class AttributeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [new TwigFunction('lag_attributes', [AttributeHelper::class, 'createAttributes'])];
    }
}
