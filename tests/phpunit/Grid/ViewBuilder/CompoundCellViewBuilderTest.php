<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\CellViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CompoundCellViewBuilder;
use LAG\AdminBundle\Metadata\Compound;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Resource;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Metadata\Update;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
        $data = new \stdClass();
        $cellView = new CellView(name: 'some_view');
        $childView = new CellView(name: 'some_child_view');

        $resource = new Resource(properties: ['some_property' => $property, 'child' => $child]);
        $operation = (new Update())->withResource($resource);

        $this->decorated
            ->expects(self::exactly(2))
            ->method('buildCell')
            ->willReturnMap([
                [$operation, $grid, $child, $data, [], $childView],
                [$operation, $grid, $property, $data, [
                    'some' => 'context',
                    'resource' => $resource,
                    'children' => [$childView],
                ], $cellView],
            ])
        ;

        $this->builder->buildCell($operation, $grid, $property, $data, ['some' => 'context', 'resource' => $resource]);
    }

    #[Test]
    public function itDoesNotBuildNotCompoundProperties(): void
    {
        $grid = new Grid(name: 'some_grid');
        $property = new Text();
        $data = new \stdClass();
        $cellView = new CellView(name: 'some_view');

        $resource = new Resource();
        $operation = (new Update())->withResource($resource);

        $this->decorated
            ->expects(self::once())
            ->method('buildCell')
            ->with($operation, $grid, $property, $data, [])
            ->willReturn($cellView)
        ;

        $this->builder->buildCell($operation, $grid, $property, $data);
    }

    #[Test]
    public function itDoesNotBuildCompoundPropertiesIfContextIsSet(): void
    {
        $grid = new Grid(name: 'some_grid');
        $property = new Compound(properties: []);
        $data = new \stdClass();
        $cellView = new CellView(name: 'some_view');

        $resource = new Resource();
        $operation = (new Update())->withResource($resource);

        $this->decorated
            ->expects(self::once())
            ->method('buildCell')
            ->with($operation, $grid, $property, $data, ['children' => 'set'])
            ->willReturn($cellView)
        ;

        $this->builder->buildCell($operation, $grid, $property, $data, ['children' => 'set']);
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(CellViewBuilderInterface::class);
        $this->builder = new CompoundCellViewBuilder($this->decorated);
    }
}
