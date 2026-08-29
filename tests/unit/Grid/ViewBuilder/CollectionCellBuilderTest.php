<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\CellBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CollectionCellBuilder;
use LAG\AdminBundle\Metadata\Attribute\Collection;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\PropertyInterface;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Runtime\EscaperRuntime;

final class CollectionCellBuilderTest extends TestCase
{
    private CollectionCellBuilder $builder;
    private MockObject $decorated;
    private MockObject $dataMapper;

    #[Test]
    public function itDelegatesANonCollectionProperty(): void
    {
        $property = new Text(name: 'title');
        $cellView = $this->createCellView('title');

        $this->dataMapper
            ->expects($this->never())
            ->method('getPropertyValue')
        ;
        $this->decorated
            ->expects($this->once())
            ->method('buildCell')
            ->willReturn($cellView)
        ;

        self::assertSame($cellView, $this->build($property, 'a title'));
    }

    #[Test]
    public function itBuildsOneChildPerEntry(): void
    {
        $property = new Collection(name: 'tags', entryProperty: new Text(name: 'tag', propertyPath: 'name'));
        $lastContext = [];

        $this->dataMapper
            ->expects($this->exactly(2))
            ->method('getPropertyValue')
            ->willReturnOnConsecutiveCalls('php', 'symfony')
        ;
        $this->decorated
            ->expects($this->exactly(3))
            ->method('buildCell')
            ->willReturnCallback(function (mixed ...$arguments) use (&$lastContext): CellView {
                $lastContext = $arguments[4] ?? [];

                return $this->createCellView('tags');
            })
        ;

        $this->build($property, ['first', 'second']);

        self::assertCount(2, $lastContext['children']);
    }

    /**
     * Nothing requires an entry property to be named, so the cell view built for an entry has to accept a
     * null name rather than fail on a type error.
     */
    #[Test]
    public function itBuildsAnEntryWithoutName(): void
    {
        $property = new Collection(name: 'tags', entryProperty: new Text(propertyPath: 'name'));
        $entryNames = [];

        $this->dataMapper
            ->method('getPropertyValue')
            ->willReturn('php')
        ;
        $this->decorated
            ->method('buildCell')
            ->willReturnCallback(function (mixed ...$arguments) use (&$entryNames): CellView {
                $builtProperty = $arguments[2];
                self::assertInstanceOf(PropertyInterface::class, $builtProperty);
                $entryNames[] = $builtProperty->getName();

                return $this->createCellView($builtProperty->getName());
            })
        ;

        $this->build($property, ['first']);

        self::assertSame([null, 'tags'], $entryNames);
    }

    #[Test]
    public function itFailsOnNonIterableData(): void
    {
        $property = new Collection(name: 'tags', entryProperty: new Text(name: 'tag'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The collection property "tags" requires iterable data, got "string"');

        $this->build($property, 'not iterable');
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(CellBuilderInterface::class);
        $this->dataMapper = $this->createMock(DataMapperInterface::class);
        $this->builder = new CollectionCellBuilder($this->decorated, $this->dataMapper);
    }

    private function build(PropertyInterface $property, mixed $data): CellView
    {
        return $this->builder->buildCell(
            new Update(),
            new Grid(name: 'some_grid'),
            $property,
            $data,
        );
    }

    private function createCellView(?string $name): CellView
    {
        return new CellView(name: $name, attributes: new ComponentAttributes([], new EscaperRuntime()));
    }
}
