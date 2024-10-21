<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\Bridge\Doctrine\ORM\Metadata\MetadataHelperInterface;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor\ORMProcessor;
use LAG\AdminBundle\Bridge\Doctrine\ORM\State\Provider\PaginationProvider;
use LAG\AdminBundle\Tests\ContainerTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BridgeServicesTest extends TestCase
{
    use ContainerTestTrait;

    #[Test]
    public function serviceExists(): void
    {
        // Doctrine ORM
        self::assertService(MetadataHelperInterface::class);
        self::assertService(ORMProcessor::class);
        self::assertService(PaginationProvider::class);
    }
}
