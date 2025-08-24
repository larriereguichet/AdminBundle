<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Grid\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\CellViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\DataCellViewBuilder;
use LAG\AdminBundle\Metadata\Grid;
use LAG\AdminBundle\Metadata\Text;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DataCellViewBuilderTest extends TestCase
{
    private DataCellViewBuilder $cellBuilder;
    private MockObject $decorated;
    private MockObject $dataMapper;
    private MockObject $transformerRegistry;

    #[Test]
    public function itBuildACellViewWithData(): void
    {
        $grid = new Grid();
        $property = new Text(dataTransformer: 'my_transformer');
        $data = new \stdClass();
        $context = ['some_option' => 'some_value'];

        $cell = new CellView(name: 'cell view');
        $operation = new Update();

        $dataTransformer = $this->createMock(DataTransformerInterface::class);
        $dataTransformer->expects($this->once())
            ->method('transform')
            ->with($property, 'some data')
            ->willReturn('some transformed data')
        ;

        $this->dataMapper
            ->expects($this->once())
            ->method('getValue')
            ->with($property, $data)
            ->willReturn('some data')
        ;
        $this->transformerRegistry
            ->expects($this->once())
            ->method('get')
            ->with($property->getDataTransformer())
            ->willReturn($dataTransformer)
        ;
        $this->decorated
            ->expects($this->once())
            ->method('buildCell')
            ->with($operation, $grid, $property, 'some transformed data', $context)
            ->willReturn($cell)
        ;

        $result = $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);

        self::assertEquals($cell, $result);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(CellViewBuilderInterface::class);
        $this->dataMapper = $this->createMock(DataMapperInterface::class);
        $this->transformerRegistry = $this->createMock(DataTransformerRegistryInterface::class);
        $this->cellBuilder = new DataCellViewBuilder(
            $this->decorated,
            $this->dataMapper,
            $this->transformerRegistry,
        );
    }
}
