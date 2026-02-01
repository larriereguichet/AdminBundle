<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\Grid\ViewBuilder;

use LAG\AdminBundle\Metadata\Attribute\Grid;
use LAG\AdminBundle\Metadata\Attribute\Text;
use LAG\AdminBundle\Metadata\Attribute\Update;
use LAG\AdminBundle\Metadata\DataTransformer\DataTransformerInterface;
use LAG\AdminBundle\Metadata\Registry\DataTransformerRegistryInterface;
use LAG\AdminBundle\Grid\ViewFactory\CellBuilderInterface;
use LAG\AdminBundle\Grid\ViewFactory\DataCellBuilder;
use LAG\AdminBundle\Grid\View\Cell;
use LAG\AdminBundle\Resource\DataMapper\DataMapperInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DataCellViewBuilderTest extends TestCase
{
    private DataCellBuilder $cellBuilder;
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

        $cell = new Cell(name: 'cell view');
        $operation = new Update();

        $dataTransformer = $this->createMock(DataTransformerInterface::class);
        $dataTransformer->expects($this->once())
            ->method('transform')
            ->with($property, 'some data')
            ->willReturn('some transformed data')
        ;

        $this->dataMapper
            ->expects($this->once())
            ->method('getPropertyValue')
            ->with($data, $property)
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
        $this->decorated = $this->createMock(CellBuilderInterface::class);
        $this->dataMapper = $this->createMock(DataMapperInterface::class);
        $this->transformerRegistry = $this->createMock(DataTransformerRegistryInterface::class);
        $this->cellBuilder = new DataCellBuilder(
            $this->decorated,
            $this->dataMapper,
            $this->transformerRegistry,
        );
    }
}
