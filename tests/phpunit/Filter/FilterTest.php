<?php

namespace LAG\AdminBundle\Tests\Filter;

use LAG\AdminBundle\Filter\Filter;
use LAG\AdminBundle\Tests\TestCase;

class FilterTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $filter = new Filter(
            'my_little_filter',
            3656.23,
            'string',
            'field',
            '=',
            'and'
        );

        $this->assertEquals('my_little_filter', $filter->getName());
        $this->assertEquals(3656.23, $filter->getValue());
        $this->assertEquals('string', $filter->getType());
        $this->assertEquals('field', $filter->getPath());
        $this->assertEquals('=', $filter->getComparator());
        $this->assertEquals('and', $filter->getOperator());
    }
}
