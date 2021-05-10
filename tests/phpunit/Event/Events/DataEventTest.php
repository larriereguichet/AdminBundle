<?php

namespace LAG\AdminBundle\Tests\Event\Events;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Event\Events\DataEvent;
use LAG\AdminBundle\Exception\Filter\FilterMissingException;
use LAG\AdminBundle\Filter\FilterInterface;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DataEventTest extends TestCase
{
    public function testEvent(): void
    {
        $admin = $this->createMock(AdminInterface::class);
        $request = new Request();

        $filter = $this->createMock(FilterInterface::class);
        $filter
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('my_filter')
        ;

        $event = new DataEvent($admin, $request);

        $this->assertEquals($admin, $event->getAdmin());
        $this->assertEquals($request, $event->getRequest());

        $event->addFilter($filter);
        $this->assertContains($filter, $event->getFilters());
        $this->assertTrue($event->hasFilter('my_filter'));

        $event->removeFilter('my_filter');
        $this->assertNotContains($filter, $event->getFilters());
        $this->assertFalse($event->hasFilter('my_filter'));
        $this->assertCount(0, $event->getFilters());

        $this->assertExceptionRaised(FilterMissingException::class, function () use ($event) {
            $event->removeFilter('missing_filter');
        });

        $filterForm = $this->createMock(FormInterface::class);
        $this->assertNull($event->getFilterForm());
        $event->setFilterForm($filterForm);
        $this->assertEquals($filterForm, $event->getFilterForm());

        $event->removeFilterForm();
        $this->assertNull($event->getFilterForm());

        $this->assertEquals([], $event->getOrderBy());
        $event->addOrderBy('title', 'desc');
        $this->assertEquals(['title' => 'desc'], $event->getOrderBy());
        $event->removeOrderBy('title');
        $this->assertEquals([], $event->getOrderBy());

        $data = new \stdClass();
        $data->test = true;
        $this->assertNull($event->getData());
        $event->setData($data);
        $this->assertEquals($event->getData(), $data);
    }
}
