<?php

namespace LAG\AdminBundle\Tests\Utils;

use LAG\AdminBundle\Tests\TestCase;
use LAG\Component\StringUtils\StringUtils;

class StringUtilsTest extends TestCase
{
    public function testCamelize()
    {
        $this->assertEquals(
            'MyService',
            StringUtils::camelize('my_service')
        );
        $this->assertEquals(
            'My-service',
            StringUtils::camelize('My-service')
        );
    }

    public function testUnderscore()
    {
        $this->assertEquals(
            'my_little_service',
            StringUtils::underscore('MyLittleService')
        );
    }

    public function testStartsWith()
    {
        $this->assertTrue(StringUtils::startsWith('MyLittleService', 'M'));
        $this->assertTrue(StringUtils::startsWith('MyLittleService', 'MyLittle'));
    }
}
