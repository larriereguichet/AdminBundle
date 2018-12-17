<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Field;

use DateTime;
use LAG\AdminBundle\Field\Field\Date;
use LAG\AdminBundle\Tests\AdminTestBase;

class DateTest extends AdminTestBase
{
    public function testRender()
    {
        $linkField = new Date('my-field');

        $this->setPrivateProperty($linkField, 'options', [
            'format' => 'd/m/Y',
        ]);

        $now = new DateTime();
        $content = $linkField->render($now);

        $this->assertEquals($now->format('d/m/Y'), $content);
    }

    public function testRenderInvalidValue()
    {
        $linkField = new Date('my-field');

        $this->assertExceptionRaised(\Exception::class, function () use ($linkField) {
            $linkField->render('2017-10-05');
        });
    }
}
