<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Behaviors;

use LAG\AdminBundle\Admin\Behaviors\TranslationKeyTrait;
use LAG\AdminBundle\Tests\AdminTestBase;

class TranslationKeyTraitTest extends AdminTestBase
{
    use TranslationKeyTrait;

    public function testGetTranslationKey()
    {
        $this->assertEquals(
            'test.ship.list',
            $this->getTranslationKey('test.{admin}.{key}', 'list', 'ship')
        );
    }
}
