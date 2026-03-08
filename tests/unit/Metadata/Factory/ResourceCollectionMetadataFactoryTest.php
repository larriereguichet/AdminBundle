<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Factory\ResourceCollectionMetadataFactory;
use LAG\AdminBundle\Tests\Application\Entity\Publisher;
use LAG\AdminBundle\Tests\Unit\ApplicationTestTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceCollectionMetadataFactoryTest extends TestCase
{
    use ApplicationTestTrait;

    #[Test]
    public function itCreatesResourceMetadataFromMappingDirectory(): void
    {
        $metadataFactory = new ResourceCollectionMetadataFactory([
            $this->getTestApplicationPath().'/config/admin',
            $this->getTestApplicationPath().'/src/Entity',
        ]);
        $resources = $metadataFactory->createMetadata();

        self::assertEquals([
            'admin.publisher' => new Resource(
                shortName: 'publisher',
                applicationName: 'admin',
                resourceClass: Publisher::class,
            ),
        ], $resources);
    }
}
