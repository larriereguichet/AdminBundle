<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Link;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LinkTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultProperties(): void
    {
        $link = new Link(name: 'edit', route: 'admin.book.update');

        self::assertSame('edit', $link->getName());
        self::assertNull($link->getTemplate());
        self::assertSame('lag_admin:link', $link->getComponent());
        self::assertFalse($link->isSortable());
        self::assertSame('admin.book.update', $link->getRoute());
        self::assertSame([], $link->getRouteParameters());
        self::assertNull($link->getOperation());
        self::assertNull($link->getType());
        self::assertNull($link->getUrl());
        self::assertNull($link->getText());
        self::assertNull($link->getTextPath());
        self::assertNull($link->getIcon());
        self::assertNull($link->getWorkflow());
        self::assertNull($link->getWorkflowTransition());
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $link = new Link(name: 'edit', route: 'admin.book.update');

        $new = $link->withRoute('admin.book.show');
        self::assertNotSame($link, $new);
        self::assertSame('admin.book.show', $new->getRoute());
        self::assertSame('admin.book.update', $link->getRoute());

        $new = $link->withRouteParameters(['id' => 'id']);
        self::assertNotSame($link, $new);
        self::assertSame(['id' => 'id'], $new->getRouteParameters());

        $new = $link->withOperation('update');
        self::assertNotSame($link, $new);
        self::assertSame('update', $new->getOperation());

        $new = $link->withType('danger');
        self::assertNotSame($link, $new);
        self::assertSame('danger', $new->getType());

        $new = $link->withUrl('https://example.com');
        self::assertNotSame($link, $new);
        self::assertSame('https://example.com', $new->getUrl());

        $new = $link->withText('Edit');
        self::assertNotSame($link, $new);
        self::assertSame('Edit', $new->getText());

        $new = $link->withTextPath('title');
        self::assertNotSame($link, $new);
        self::assertSame('title', $new->getTextPath());

        $new = $link->withIcon('pencil');
        self::assertNotSame($link, $new);
        self::assertSame('pencil', $new->getIcon());

        $new = $link->withWorkflow('publishing');
        self::assertNotSame($link, $new);
        self::assertSame('publishing', $new->getWorkflow());

        $new = $link->withWorkflowTransition('publish');
        self::assertNotSame($link, $new);
        self::assertSame('publish', $new->getWorkflowTransition());
    }
}
