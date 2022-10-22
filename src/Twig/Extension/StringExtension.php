<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Extension;

use Symfony\Component\String\Inflector\EnglishInflector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class StringExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('pluralize', [$this, 'pluralize']),
        ];
    }

    public function pluralize(string $singular): string
    {
        return (new EnglishInflector())->pluralize($singular)[0];
    }
}
