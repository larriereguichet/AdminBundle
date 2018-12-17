<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Utils;

use LAG\AdminBundle\Tests\AdminTestBase;
use LAG\AdminBundle\Utils\FieldTypeGuesser;

class FieldTypeGuesserTest extends AdminTestBase
{
    public function testGetTypeAndOptions()
    {
        $guesser = new FieldTypeGuesser();
        $type = $guesser->getTypeAndOptions('string');

        // test string
        $this->assertEquals('string', $type['type']);
        $this->assertEquals([
            'length' => 100,
        ], $type['options']);

        // test boolean
        $type = $guesser->getTypeAndOptions('boolean');

        $this->assertEquals('boolean', $type['type']);

        // test datetime
        $type = $guesser->getTypeAndOptions('datetime');
        $this->assertEquals('date', $type['type']);
    }
}
