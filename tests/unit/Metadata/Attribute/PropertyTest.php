<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Metadata\Attribute;

use LAG\AdminBundle\Metadata\Attribute\Property;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PropertyTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultValues(): void
    {
        $property = new Property(name: 'title');

        self::assertSame('title', $property->getName());
        self::assertNull($property->getPropertyPath());
        self::assertNull($property->getLabel());
        self::assertNull($property->getTemplate());
        self::assertTrue($property->isSortable());
        self::assertFalse($property->isTranslatable());
        self::assertSame([], $property->getAttributes());
        self::assertSame([], $property->getRowAttributes());
        self::assertSame([], $property->getHeaderAttributes());
        self::assertNull($property->getDataTransformer());
        self::assertNull($property->getRoles());
        self::assertNull($property->getCondition());
        self::assertNull($property->getSortingPath());
    }

    #[Test]
    public function itReturnsAttributeByKey(): void
    {
        $property = (new Property(name: 'title'))->withAttribute('class', 'bold');

        self::assertSame('bold', $property->getAttribute('class'));
        self::assertNull($property->getAttribute('missing'));
    }

    #[Test]
    public function itReturnsImmutableCopiesForWithMethods(): void
    {
        $property = new Property(name: 'title');

        $new = $property->withName('author');
        self::assertNotSame($property, $new);
        self::assertSame('author', $new->getName());
        self::assertSame('title', $property->getName());

        $new = $property->withLabel('Book Title');
        self::assertNotSame($property, $new);
        self::assertSame('Book Title', $new->getLabel());

        $new = $property->withPropertyPath('book.title');
        self::assertNotSame($property, $new);
        self::assertSame('book.title', $new->getPropertyPath());

        $new = $property->withTemplate('@App/cell.html.twig');
        self::assertNotSame($property, $new);
        self::assertSame('@App/cell.html.twig', $new->getTemplate());

        $new = $property->withSortable(false);
        self::assertNotSame($property, $new);
        self::assertFalse($new->isSortable());

        $new = $property->withTranslatable(true);
        self::assertNotSame($property, $new);
        self::assertTrue($new->isTranslatable());

        $new = $property->withAttributes(['class' => 'text-bold']);
        self::assertNotSame($property, $new);
        self::assertSame(['class' => 'text-bold'], $new->getAttributes());

        $new = $property->withRowAttributes(['class' => 'row']);
        self::assertNotSame($property, $new);
        self::assertSame(['class' => 'row'], $new->getRowAttributes());

        $new = $property->withHeaderAttributes(['scope' => 'col']);
        self::assertNotSame($property, $new);
        self::assertSame(['scope' => 'col'], $new->getHeaderAttributes());

        $new = $property->withDataTransformer('App\DataTransformer\MyTransformer');
        self::assertNotSame($property, $new);
        self::assertSame('App\DataTransformer\MyTransformer', $new->getDataTransformer());

        $new = $property->withPermissions(['ROLE_ADMIN']);
        self::assertNotSame($property, $new);
        self::assertSame(['ROLE_ADMIN'], $new->getRoles());

        $new = $property->withCondition('workflow.active');
        self::assertNotSame($property, $new);
        self::assertSame('workflow.active', $new->getCondition());

        $new = $property->withSortingPath('title');
        self::assertNotSame($property, $new);
        self::assertSame('title', $new->getSortingPath());

        $new = $property->withAttribute('id', 'my-prop');
        self::assertNotSame($property, $new);
        self::assertSame('my-prop', $new->getAttribute('id'));
    }
}
