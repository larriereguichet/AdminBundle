<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\Resource\Context\ResourceContext;
use LAG\AdminBundle\Resource\Context\ResourceContextInterface;
use LAG\AdminBundle\Resource\Registry\ResourceRegistry;
use LAG\AdminBundle\Resource\Registry\ResourceRegistryInterface;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceServicesTest extends TestCase
{
    use ContainerTestTrait;

    #[Test]
    public function serviceExists(): void
    {
        self::assertService(ResourceRegistryInterface::class);
        self::assertNoService(ResourceRegistry::class);

        self::assertService(ResourceContextInterface::class);
        self::assertNoService(ResourceContext::class);
    }
}
