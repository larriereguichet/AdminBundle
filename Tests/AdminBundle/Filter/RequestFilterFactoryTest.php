<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Filter;

use LAG\AdminBundle\Action\Configuration\ActionConfiguration;
use LAG\AdminBundle\Filter\Factory\RequestFilterFactory;
use LAG\AdminBundle\Filter\PagerfantaFilter;
use LAG\AdminBundle\Filter\RequestFilterInterface;
use LAG\AdminBundle\Tests\AdminTestBase;

class RequestFilterFactoryTest extends AdminTestBase
{
    public function testCreate()
    {
        $configuration = $this->createMock(ActionConfiguration::class);
        $configuration
            ->method('hasParameter')
            ->with('pager')
            ->willReturn(false)
        ;

        $factory = new RequestFilterFactory();
        $filter = $factory->create($configuration);
        $this->assertInstanceOf(
            RequestFilterInterface::class,
            $filter
        );
    }

    public function testCreateWithPager()
    {
        $configuration = $this->createMock(ActionConfiguration::class);
        $configuration
            ->method('hasParameter')
            ->with('pager')
            ->willReturn(true)
        ;
        $configuration
            ->method('getParameter')
            ->with('pager')
            ->willReturn('pagerfanta');

        $factory = new RequestFilterFactory();
        $filter = $factory->create($configuration);
        $this->assertInstanceOf(
            PagerfantaFilter::class,
            $filter
        );
    }
}
