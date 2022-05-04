<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;

trait ApplicationAware
{
    protected ApplicationConfiguration $applicationConfiguration;

    public function getApplicationConfiguration(): ApplicationConfiguration
    {
        return $this->applicationConfiguration;
    }

    public function setApplicationConfiguration(ApplicationConfiguration $applicationConfiguration): void
    {
        $this->applicationConfiguration = $applicationConfiguration;
    }
}
