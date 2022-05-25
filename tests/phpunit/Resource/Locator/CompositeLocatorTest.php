<?php

namespace LAG\AdminBundle\Tests\Resource\Locator;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Admin;
use LAG\AdminBundle\Resource\Locator\CompositeLocator;
use LAG\AdminBundle\Resource\Locator\ResourceLocatorInterface;
use LAG\AdminBundle\Tests\TestCase;

class CompositeLocatorTest extends TestCase
{
    public function testLocate(): void
    {
        $locator1 = $this->createMock(ResourceLocatorInterface::class);
        $locator1
            ->expects($this->once())
            ->method('locate')
            ->with('/a/directory')
            ->willReturn([
                new Admin('an_admin'),
            ])
        ;
        $locator2 = $this->createMock(ResourceLocatorInterface::class);
        $locator2
            ->expects($this->once())
            ->method('locate')
            ->with('/a/directory')
            ->willReturn([
                new Admin('an_other_admin'),
            ])
        ;

        $compositeLocator = $this->createLocator([$locator1, $locator2]);
        $this->assertEquals([
            new Admin('an_admin'),
            new Admin('an_other_admin'),
        ], $compositeLocator->locate('/a/directory'));
    }

    public function testLocateWithNoLocators(): void
    {
        $compositeLocator = $this->createLocator([]);
        $this->assertEquals([], $compositeLocator->locate('/a/directory'));
    }

    public function testWithWrongLocator(): void
    {
        $wrongLocator = $this->createMock(ResourceLocatorInterface::class);
        $wrongLocator
            ->expects($this->once())
            ->method('locate')
            ->with('/a/directory')
            ->willReturn([
                new \stdClass(),
            ])
        ;

        $this->expectException(Exception::class);
        $compositeLocator = $this->createLocator([$wrongLocator]);
        $compositeLocator->locate('/a/directory');
    }

    public function createLocator(array $locators): CompositeLocator
    {
        return new CompositeLocator($locators);
    }
}
