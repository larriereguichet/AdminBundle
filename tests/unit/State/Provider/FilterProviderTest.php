<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Unit\State\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Metadata\Attribute\Index;
use LAG\AdminBundle\Metadata\Attribute\TextFilter;
use LAG\AdminBundle\State\Provider\FilterProvider;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class FilterProviderTest extends TestCase
{
    private FilterProvider $provider;
    private MockObject $decorated;
    private MockObject $filterApplicator;

    #[Test]
    public function itProvidesData(): void
    {
        $filter = new TextFilter(name: 'my_filter');
        $operation = (new Index())->withFilter($filter);
        $uriVariables = ['some' => 'value'];
        $context = [
            'some_context' => 'context_value',
            'filters' => [
                'my_filter' => 'some_filter_value',
            ],
        ];

        $data = $this->createStub(QueryBuilder::class);

        $this->decorated
            ->expects($this->once())
            ->method('provide')
            ->with($operation, $uriVariables, $context)
            ->willReturn($data)
        ;
        $this->filterApplicator
            ->expects($this->once())
            ->method('supports')
            ->with($operation, $filter, $data, 'some_filter_value')
            ->willReturn(true)
        ;
        $this->filterApplicator
            ->expects($this->once())
            ->method('apply')
            ->with($operation, $filter, $data, 'some_filter_value')
        ;

        $this->provider->provide($operation, $uriVariables, $context);
    }

    #[Test]
    public function itDoesNotProvidesNonQueryBuilderData(): void
    {
        $this->decorated
            ->expects($this->once())
            ->method('provide')
            ->with(new Index(), ['some' => 'value'], ['some_context' => 'context_value'])
            ->willReturn(new ArrayCollection([new \stdClass()]))
        ;
        $this->filterApplicator
            ->expects($this->never())
            ->method('supports')
        ;
        $this->filterApplicator
            ->expects($this->never())
            ->method('apply')
        ;

        $this->provider->provide(new Index(), ['some' => 'value'], ['some_context' => 'context_value']);
    }

    #[Test]
    public function itSkipsFilterWhenApplicatorDoesNotSupport(): void
    {
        $filter = new TextFilter(name: 'my_filter');
        $operation = (new Index())->withFilter($filter);
        $context = ['filters' => ['my_filter' => 'some_value']];
        $data = $this->createStub(QueryBuilder::class);

        $this->decorated->method('provide')->willReturn($data);
        $this->filterApplicator
            ->expects($this->once())
            ->method('supports')
            ->willReturn(false)
        ;
        $this->filterApplicator->expects($this->never())->method('apply');

        $this->provider->provide($operation, [], $context);
    }

    #[Test]
    public function itSkipsUnknownFilters(): void
    {
        $operation = new Index();
        $context = ['filters' => ['unknown_filter' => 'some_value']];
        $data = $this->createStub(QueryBuilder::class);

        $this->decorated->method('provide')->willReturn($data);
        $this->filterApplicator->expects($this->never())->method('supports');
        $this->filterApplicator->expects($this->never())->method('apply');

        $this->provider->provide($operation, [], $context);
    }

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(ProviderInterface::class);
        $this->filterApplicator = $this->createMock(FilterApplicatorInterface::class);
        $this->provider = new FilterProvider(
            $this->decorated,
            $this->filterApplicator,
        );
    }
}
