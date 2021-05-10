<?php

namespace LAG\AdminBundle\Tests\DataProvider;

use LAG\AdminBundle\Admin\AdminAwareInterface;
use LAG\AdminBundle\DataProvider\DataProviderInterface;

interface AdminAwareDataProviderInterface extends DataProviderInterface, AdminAwareInterface
{
}
