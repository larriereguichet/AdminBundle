<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Tests\Functional;

use LAG\AdminBundle\LAGAdminBundle;
use Nyholm\BundleTest\BaseBundleTestCase;

class HomeActionFunctionalTest extends BaseBundleTestCase
{
    public function testDashboard()
    {
        // TODO add a functional test
        // $client = static::createClient();
        $this->assertTrue(true);
    }

    protected function getBundleClass()
    {
        return LAGAdminBundle::class;
    }
}
