<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Grid\ViewBuilder;

use LAG\AdminBundle\Grid\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Grid\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\View\CellView;
use LAG\AdminBundle\Grid\ViewBuilder\CellViewBuilderInterface;
use LAG\AdminBundle\Grid\ViewBuilder\DataCellViewBuilder;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use LAG\AdminBundle\Resource\Metadata\Grid;
use LAG\AdminBundle\Resource\Metadata\Text;
use LAG\AdminBundle\Resource\Metadata\Update;
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

        $dataTransformer = self::createMock(DataTransformerInterface::class);
        $dataTransformer->expects(self::once())
            ->method('transform')
            ->with($property, 'some data')
            ->willReturn('some transformed data')
        ;

        $this->dataMapper
            ->expects(self::once())
            ->method('getValue')
            ->with($property, $data)
            ->willReturn('some data')
        ;
        $this->transformerRegistry
            ->expects(self::once())
            ->method('get')
            ->with($property->getDataTransformer())
            ->willReturn($dataTransformer)
        ;
        $this->decorated
            ->expects(self::once())
            ->method('buildCell')
            ->with($operation, $grid, $property, 'some transformed data', $context)
            ->willReturn($cell)
        ;

        $result = $this->cellBuilder->buildCell($operation, $grid, $property, $data, $context);

        self::assertEquals($cell, $result);
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(CellViewBuilderInterface::class);
        $this->dataMapper = self::createMock(DataMapperInterface::class);
        $this->transformerRegistry = self::createMock(DataTransformerRegistryInterface::class);
        $this->cellBuilder = new DataCellViewBuilder(
            $this->decorated,
            $this->dataMapper,
            $this->transformerRegistry,
        );
    }
}
