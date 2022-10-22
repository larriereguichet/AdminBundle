<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\State;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\ORMDataProcessor;
use LAG\AdminBundle\Tests\TestCase;

class ORMDataProcessorTest extends TestCase
{
    public function testService(): void
    {
        $this->assertServiceExists(ORMDataProcessor::class);
    }
}
