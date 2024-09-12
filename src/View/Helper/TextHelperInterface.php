<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

interface TextHelperInterface
{
    public function pluralize(string $singular): string;

    public function richText(string $richText): string;
}
