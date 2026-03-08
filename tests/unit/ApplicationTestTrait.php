<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit;

trait ApplicationTestTrait
{
    protected function getTestApplicationPath(): string
    {
        return __DIR__.'/../app';
    }
}
