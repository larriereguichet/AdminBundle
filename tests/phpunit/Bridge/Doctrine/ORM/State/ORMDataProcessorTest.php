<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Bridge\Doctrine\ORM\State;

use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Tests\TestCase;

class ORMDataProcessorTest extends TestCase
{
    public function testService(): void
    {
        $this->assertServiceExists(ORMProcessor::class);
    }
}
