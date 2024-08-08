<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Metadata\Resource;

use LAG\AdminBundle\Resource\Metadata\Link;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LinkTest extends TestCase
{
    #[Test]
    public function itReturnsProperties(): void
    {
        $link = new Link(
            name: 'My property',
            label: 'My Property Label',
            sortable: true,
            translatable: true,
            translationDomain: 'admin',
            attributes: ['an' => 'attribute'],
            headerAttributes: ['an' => 'attribute'],
            route: 'my_route',
            routeParameters: ['my_param' => 'value'],
            application: 'an_application',
            resource: 'a_resource',
            operation: 'an_operation',
            type: 'custom',
            url: 'property.com',
            icon: 'icon',
        );

        self::assertEquals('My property', $link->getName());
        self::assertEquals('My Property Label', $link->getLabel());
        self::assertTrue($link->isSortable());
        self::assertTrue($link->isTranslatable());
        self::assertEquals(['an' => 'attribute'], $link->getAttributes());
        self::assertEquals(['an' => 'attribute'], $link->getHeaderAttributes());
        self::assertEquals('my_route', $link->getRoute());
        self::assertEquals(['my_param' => 'value'], $link->getRouteParameters());
        self::assertEquals('an_application', $link->getApplication());
        self::assertEquals('a_resource', $link->getResource());
        self::assertEquals('an_operation', $link->getOperation());
        self::assertEquals('property.com', $link->getUrl());
        self::assertEquals('custom', $link->getType());
        self::assertEquals('icon', $link->getIcon());

        $newLink = $link->withName('an_other_name');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('an_other_name', $newLink->getName());

        $newLink = $link->withLabel('An other label');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('An other label', $newLink->getLabel());

        $newLink = $link->withSortable(false);
        self::assertNotEquals($link, $newLink);
        self::assertFalse($newLink->isSortable());

        $newLink = $link->withTranslatable(false);
        self::assertNotEquals($link, $newLink);
        self::assertFalse($newLink->isTranslatable());

        $newLink = $link->withAttributes(['id' => 'property']);
        self::assertNotEquals($link, $newLink);
        self::assertEquals(['id' => 'property'], $newLink->getAttributes());

        $newLink = $link->withHeaderAttributes(['class' => 'header']);
        self::assertNotEquals($link, $newLink);
        self::assertEquals(['class' => 'header'], $newLink->getHeaderAttributes());

        $newLink = $link->withApplication('admin_application');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('admin_application', $newLink->getApplication());

        $newLink = $link->withResource('orders');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('orders', $newLink->getResource());

        $newLink = $link->withOperation('show');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('show', $newLink->getOperation());

        $newLink = $link->withRoute('order_show');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('order_show', $newLink->getRoute());

        $newLink = $link->withRouteParameters(['id' => '66']);
        self::assertNotEquals($link, $newLink);
        self::assertEquals(['id' => '66'], $newLink->getRouteParameters());

        $newLink = $link->withUrl('admin.com');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('admin.com', $newLink->getUrl());

        $newLink = $link->withType('default');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('default', $newLink->getType());

        $newLink = $link->withIcon('cross.png');
        self::assertNotEquals($link, $newLink);
        self::assertEquals('cross.png', $newLink->getIcon());
    }
}
