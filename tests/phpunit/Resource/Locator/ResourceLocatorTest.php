<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Resource\Locator;

use LAG\AdminBundle\Resource\Locator\ResourceLocator;
use LAG\AdminBundle\Tests\Fixtures\FakeResource;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResourceLocatorTest extends TestCase
{
    #[Test]
    public function itLocateResources(): void
    {
        $reflectionClass = new \ReflectionClass(FakeResource::class);

        $locator = new ResourceLocator(defaultApplication: 'some_default_application');
        $resources = $locator->locateResources($reflectionClass);
        $resources = iterator_to_array($resources);

        self::assertEquals('fake_resource', $resources[0]->getName());
        self::assertEquals('shop', $resources[0]->getApplication());
        self::assertEquals('fake_resource', $resources[1]->getName());
        self::assertEquals('admin', $resources[1]->getApplication());
    }
}
