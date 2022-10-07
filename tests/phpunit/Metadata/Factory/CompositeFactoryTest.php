<?php

namespace LAG\AdminBundle\Tests\Metadata\Factory;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Locator\CompositeLocator;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Tests\TestCase;

class CompositeFactoryTest extends TestCase
{
    public function testCreateResources(): void
    {
        $locator1 = $this->createMock(MetadataLocatorInterface::class);
        $locator1
            ->expects($this->once())
            ->method('locateCollection')
            ->with('/a/directory')
            ->willReturn([
                new AdminResource('an_admin'),
            ])
        ;
        $locator2 = $this->createMock(MetadataLocatorInterface::class);
        $locator2
            ->expects($this->once())
            ->method('locateCollection')
            ->with('/a/directory')
            ->willReturn([
                new AdminResource('an_other_admin'),
            ])
        ;

        $compositeLocator = $this->createLocator([$locator1, $locator2]);
        $this->assertEquals([
            new AdminResource('an_admin'),
            new AdminResource('an_other_admin'),
        ], $compositeLocator->locateCollection('/a/directory'));
    }

    public function testLocateWithNoLocators(): void
    {
        $compositeLocator = $this->createLocator([]);
        $this->assertEquals([], $compositeLocator->locateCollection('/a/directory'));
    }

    public function testWithWrongLocator(): void
    {
        $wrongLocator = $this->createMock(MetadataLocatorInterface::class);
        $wrongLocator
            ->expects($this->once())
            ->method('locateCollection')
            ->with('/a/directory')
            ->willReturn([
                new \stdClass(),
            ])
        ;

        $this->expectException(Exception::class);
        $compositeLocator = $this->createLocator([$wrongLocator]);
        $compositeLocator->locateCollection('/a/directory');
    }

    public function createLocator(array $locators): CompositeLocator
    {
        return new \LAG\AdminBundle\Metadata\Locator\CompositeLocator($locators);
    }
}
