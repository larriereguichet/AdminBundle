<?php

namespace LAG\AdminBundle\Tests\AdminBundle\Admin\Behaviors;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Behaviors\AdminAwareTrait;
use LAG\AdminBundle\Tests\AdminTestBase;

class AdminAwareTraitTest extends AdminTestBase
{
    use AdminAwareTrait;

    public function testGettersAndSetters()
    {
        $admin = $this->getMockWithoutConstructor(AdminInterface::class);
        $this->setAdmin($admin);

        $this->assertEquals($admin, $this->getAdmin());
    }
}
