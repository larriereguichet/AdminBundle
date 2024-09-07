<?php
declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\CellViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CompoundCellViewBuilder;
use LAG\AdminBundle\Resource\Metadata\Compound;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\Resource;
use LAG\AdminBundle\Resource\Metadata\Text;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

final class CompoundCellViewBuilderTest extends TestCase
{
    private CompoundCellViewBuilder $builder;
    private MockObject $decorated;

    #[Test]
    public function itBuildsNotCompoundProperties(): void
    {
        $grid = new Grid(name: 'some_grid');
        $child = new Text(name: 'child');
        $property = new Compound(name: 'some_property', properties: ['child']);
        $data = new stdClass();
        $cellView = new CellView(name: 'some_view');
        $childView = new CellView(name: 'some_child_view');

        $resource = new Resource(properties: ['some_property' => $property, 'child' => $child]);

        $this->decorated
            ->expects(self::exactly(2))
            ->method('buildCell')
            ->willReturnMap([
                [$grid, $child, $data, [], $childView],
                [$grid, $property, $data, [
                    'some' => 'context',
                    'resource' => $resource,
                    'children' => [$childView],
                ], $cellView],
            ])
        ;

        $this->builder->buildCell($grid, $property, $data, ['some' => 'context', 'resource' => $resource]);
    }


    #[Test]
    public function itDoesNotBuildNotCompoundProperties(): void
    {
        $grid = new Grid(name: 'some_grid');
        $property = new Text();
        $data = new stdClass();
        $cellView = new CellView(name: 'some_view');

        $this->decorated
            ->expects(self::once())
            ->method('buildCell')
            ->with($grid, $property, $data, [])
            ->willReturn($cellView)
        ;

        $this->builder->buildCell($grid, $property, $data);
    }

    #[Test]
    public function itDoesNotBuildCompoundPropertiesIfContextIsSet(): void
    {
        $grid = new Grid(name: 'some_grid');
        $property = new Compound(properties: []);
        $data = new stdClass();
        $cellView = new CellView(name: 'some_view');

        $this->decorated
            ->expects(self::once())
            ->method('buildCell')
            ->with($grid, $property, $data, ['children' => 'set'])
            ->willReturn($cellView)
        ;

        $this->builder->buildCell($grid, $property, $data, ['children' => 'set']);
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(CellViewBuilderInterface::class);
        $this->builder = new CompoundCellViewBuilder($this->decorated);
    }
}
