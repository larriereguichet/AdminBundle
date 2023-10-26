<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Locator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\AdminResource;
use LAG\AdminBundle\Metadata\Locator\CompositeLocator;
use LAG\AdminBundle\Metadata\Locator\MetadataLocatorInterface;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CompositeLocatorTest extends TestCase
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

        $kernel = $this->createMock(KernelInterface::class);

        $compositeLocator = $this->createLocator([$locator1, $locator2], $this->createMock(KernelInterface::class));
        $this->assertEquals([
            new AdminResource('an_admin'),
            new AdminResource('an_other_admin'),
        ], $compositeLocator->locateCollection('/a/directory'));
    }

    public function testLocateBundleLocators(): void
    {
        $locator = $this->createMock(MetadataLocatorInterface::class);
        $locator
            ->expects($this->once())
            ->method('locateCollection')
            ->with('/a/path/to/bundle/Entity')
            ->willReturn([
                new AdminResource('an_admin'),
            ])
        ;

        $bundle = $this->createMock(BundleInterface::class);
        $bundle
            ->expects($this->once())
            ->method('getPath')
            ->willReturn('/a/path/to/bundle')
        ;

        $kernel = $this->createMock(KernelInterface::class);
        $kernel
            ->expects($this->once())
            ->method('getBundle')
            ->with('MyBundle')
            ->willReturn($bundle)
        ;

        $compositeLocator = $this->createLocator([$locator], $kernel);
        $compositeLocator->locateCollection('@MyBundle/Entity');
    }

    public function testLocateWithNoLocators(): void
    {
        $compositeLocator = $this->createLocator([], $this->createMock(KernelInterface::class));
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
        $compositeLocator = $this->createLocator([$wrongLocator], $this->createMock(KernelInterface::class));
        $compositeLocator->locateCollection('/a/directory');
    }

    public function createLocator(array $locators, KernelInterface $kernel): CompositeLocator
    {
        return new CompositeLocator($locators, $kernel);
    }
}
