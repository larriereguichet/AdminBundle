<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\CellBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\CompoundCellBuilder;
use LAG\AdminBundle\Metadata\Attribute\Compound;
use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Resource;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Attribute\Update;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Twig\Runtime\EscaperRuntime;

final class CompoundCellViewBuilderTest extends TestCase
{
    private CompoundCellBuilder $builder;
    private MockObject $decorated;

    #[Test]
    public function itBuildsNotCompoundProperties(): void
    {
        $grid = new Grid(name: 'some_grid');
        $child = new Text(name: 'child');
        $property = new Compound(name: 'some_property', properties: ['child']);
        $data = new \stdClass();
        $cellView = new CellView(name: 'some_view', attributes: new ComponentAttributes([], new EscaperRuntime()));
        $childView = new CellView(name: 'some_child_view', attributes: new ComponentAttributes([], new EscaperRuntime()));

        $resource = new Resource(properties: ['some_property' => $property, 'child' => $child]);
        $operation = (new Update())->setResource($resource);

        $this->decorated
            ->expects($this->exactly(2))
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
        $cellView = new CellView(name: 'some_view', attributes: new ComponentAttributes([], new EscaperRuntime()));

        $resource = new Resource();
        $operation = (new Update())->setResource($resource);

        $this->decorated
            ->expects($this->once())
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
        $cellView = new CellView(name: 'some_view', attributes: new ComponentAttributes([], new EscaperRuntime()));

        $resource = new Resource();
        $operation = (new Update())->setResource($resource);

        $this->decorated
            ->expects($this->once())
            ->method('buildCell')
            ->with($operation, $grid, $property, $data, ['children' => 'set'])
            ->willReturn($cellView)
        ;

        $this->builder->buildCell($operation, $grid, $property, $data, ['children' => 'set']);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(CellBuilderInterface::class);
        $this->builder = new CompoundCellBuilder($this->decorated);
    }
}
