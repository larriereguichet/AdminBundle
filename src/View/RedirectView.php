<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View;

use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\Exception\Exception;

class RedirectView implements ViewInterface
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getTemplate(): string
    {
        throw new Exception('This method is not available for RedirectView');
    }

    public function getBase(): string
    {
        throw new Exception('This method is not available for RedirectView');
    }

    public function getActionConfiguration(): ActionConfiguration
    {
        throw new Exception('This method is not available for RedirectView');
    }

    public function getName(): string
    {
        throw new Exception('This method is not available for RedirectView');
    }

    public function getActionName(): string
    {
        throw new Exception('This method is not available for RedirectView');
    }

    public function getData()
    {
        throw new Exception('This method is not available for RedirectView');
    }

    public function getFields(): array
    {
        throw new Exception('This method is not available for RedirectView');
    }

    public function getAdminConfiguration(): AdminConfiguration
    {
        throw new Exception('This method is not available for RedirectView');
    }

    public function getForms(): array
    {
        throw new Exception('This method is not available for RedirectView');
    }
}
