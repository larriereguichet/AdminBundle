<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;

interface ApplicationAwareInterface
{
    public function setApplicationConfiguration(ApplicationConfiguration $configuration): void;
}
