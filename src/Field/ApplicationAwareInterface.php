<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;

/** @deprecated  */
interface ApplicationAwareInterface
{
    public function setApplicationConfiguration(ApplicationConfiguration $configuration): void;
}
