<?php

namespace LAG\AdminBundle\Tests\Utils;

use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Utils\StringUtils;

class StringUtilsTest extends AdminTestBase
{
    public function testGetTranslationKey()
    {
        $this->assertEquals(
            'test.tauntaun.open',
            StringUtils::getTranslationKey('test.{admin}.{key}', 'tauntaun', 'open')
        );
    }

    public function testGetActionTranslationKey()
    {
        $this->assertEquals(
            'test.tauntaun.open',
            StringUtils::getActionTranslationKey('test.{admin}.{key}', 'tauntaun', 'open')
        );
    }

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
        $this->assertTrue(StringUtils::startWith('MyLittleService', 'M'));
        $this->assertTrue(StringUtils::startWith('MyLittleService', 'MyLittle'));
    }
}
