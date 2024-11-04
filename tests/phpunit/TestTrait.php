<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests;

trait TestTrait
{
    protected static function getApplicationPath(): string
    {
        return __DIR__.'/../app';
    }
}
