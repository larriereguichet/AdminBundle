<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\State\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use LAG\AdminBundle\Filter\Applicator\FilterApplicatorInterface;
use LAG\AdminBundle\Filter\Resolver\FilterValuesResolverInterface;
use LAG\AdminBundle\Resource\Metadata\Index;
use LAG\AdminBundle\Resource\Metadata\TextFilter;
use LAG\AdminBundle\State\Provider\FilterProvider;
use LAG\AdminBundle\State\Provider\ProviderInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class FilterProviderTest extends TestCase
{
    private FilterProvider $provider;
    private MockObject $decorated;
    private MockObject $filterValuesResolver;
    private MockObject $filterApplicator;

    #[Test]
    public function itProvidesData(): void
    {
        $filter = new TextFilter(name: 'my_filter');
        $operation = (new Index())->withFilter($filter);
        $uriVariables = ['some' => 'value'];
        $context = ['some_context' => 'context_value'];

        $data = self::createMock(QueryBuilder::class);

        $this->decorated
            ->expects(self::once())
            ->method('provide')
            ->with($operation, $uriVariables, $context)
            ->willReturn($data)
        ;

        $this->filterValuesResolver
            ->expects(self::once())
            ->method('resolveFilters')
            ->with($operation->getFilters(), $context)
            ->willReturn(['my_filter' => 'my_filter_value'])
        ;

        $this->filterApplicator
            ->expects(self::once())
            ->method('supports')
            ->with($operation, $filter, $data, 'my_filter_value')
            ->willReturn(true)
        ;
        $this->filterApplicator
            ->expects(self::once())
            ->method('apply')
            ->with($operation, $filter, $data, 'my_filter_value')
        ;

        $this->provider->provide($operation, $uriVariables, $context);
    }

    #[Test]
    public function itDoesNotProvidesNonQueryBuilderData(): void
    {
        $this->decorated
            ->expects(self::once())
            ->method('provide')
            ->with(new Index(), ['some' => 'value'], ['some_context' => 'context_value'])
            ->willReturn(new ArrayCollection([new \stdClass()]))
        ;
        $this->filterValuesResolver
            ->expects(self::never())
            ->method('resolveFilters')
        ;
        $this->filterApplicator
            ->expects(self::never())
            ->method('supports')
        ;
        $this->filterApplicator
            ->expects(self::never())
            ->method('apply')
        ;

        $this->provider->provide(new Index(), ['some' => 'value'], ['some_context' => 'context_value']);
    }

    protected function setUp(): void
    {
        $this->decorated = self::createMock(ProviderInterface::class);
        $this->filterValuesResolver = self::createMock(FilterValuesResolverInterface::class);
        $this->filterApplicator = self::createMock(FilterApplicatorInterface::class);
        $this->provider = new FilterProvider(
            $this->decorated,
            $this->filterValuesResolver,
            $this->filterApplicator,
        );
    }
}
