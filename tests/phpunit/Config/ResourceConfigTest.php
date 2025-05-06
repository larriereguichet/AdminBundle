<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Config;

use LAG\AdminBundle\Config\ResourceConfig;
use LAG\AdminBundle\Metadata\Resource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceConfigTest extends TestCase
{
    #[Test]
    public function itConvertsAResourceToAnArray(): void
    {
        $resource = new Resource(name: 'my_resource');
        $config = new ResourceConfig();
        $config->addResource($resource);

        $data = $config->toArray();

        self::assertEquals('my_resource', $data['resources'][0]['name']);
        self::assertEquals('lag_admin', $config->getExtensionAlias());
    }
}
