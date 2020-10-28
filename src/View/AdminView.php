<?php

namespace LAG\AdminBundle\View;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\ActionConfiguration;
use LAG\AdminBundle\Configuration\AdminConfiguration;

class AdminView implements ViewInterface
{
    private AdminInterface $admin;
    private Template $template;
    private array $fields;
    private array $forms;

    public function __construct(AdminInterface $admin, Template $template, array $fields = [], array $forms = [])
    {
        $this->admin = $admin;
        $this->fields = $fields;
        $this->template = $template;
        $this->forms = $forms;
    }

    public function getData()
    {
        return $this->admin->getData();
    }

    public function getActionConfiguration(): ActionConfiguration
    {
        return $this->admin->getAction()->getConfiguration();
    }

    public function getActionName(): string
    {
        return $this->admin->getAction()->getName();
    }

    public function getTitle(): string
    {
        return $this->admin->getAction()->getConfiguration()->getTitle();
    }

    public function getName(): string
    {
        return $this->admin->getName();
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getAdminConfiguration(): AdminConfiguration
    {
        return $this->admin->getConfiguration();
    }

    public function getTemplate(): string
    {
        return $this->template->getTemplate();
    }

    public function getForms(): array
    {
        return $this->forms;
    }

    public function getBase(): string
    {
        return $this->template->getBase();
    }
}
