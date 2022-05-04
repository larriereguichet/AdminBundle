<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Admin\View;

use LAG\AdminBundle\Action\View\ActionView;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\View\Template\Template;
use LAG\AdminBundle\View\ViewInterface;

class AdminView implements ViewInterface
{
    private ActionView $action;

    public function __construct(
        private AdminInterface $admin,
        private Template $template,
        private array $fields = [],
        private array $forms = []
    ) {
        $this->action = $this->admin->getAction()->createView();
    }

    public function getData()
    {
        return $this->admin->getData();
    }

    public function getAction(): ActionView
    {
        return $this->action;
    }

    public function getTitle(): string
    {
        return ucfirst($this->admin->getName());
    }

    public function getName(): string
    {
        return $this->admin->getName();
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getConfiguration(): AdminConfiguration
    {
        return $this->admin->getConfiguration();
    }

    public function getTemplate(): string
    {
        return $this->template->getTemplate();
    }

    public function getBase(): string
    {
        return $this->template->getBase();
    }

    public function getForms(): array
    {
        return $this->forms;
    }

    public function getListActions(): array
    {
        return $this->action->getConfiguration()->getListActions();
    }

    public function getItemActions(): array
    {
        return $this->action->getConfiguration()->getItemActions();
    }
}
