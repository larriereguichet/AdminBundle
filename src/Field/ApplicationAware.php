<?php

namespace LAG\AdminBundle\Field;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;

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
