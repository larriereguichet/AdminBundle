<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\EventListener\Resource\InitializeResourceListener;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ListenerServicesTest extends TestCase
{
    use ContainerTestTrait;

    #[Test]
    public function servicesExists(): void
    {
        $this->assertService(InitializeResourceListener::class);
    }
}
