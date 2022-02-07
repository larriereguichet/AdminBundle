<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Admin\Configuration\AdminConfiguration;
use LAG\AdminBundle\Admin\View\ActionView;
use LAG\AdminBundle\View\Template\Template;

class AdminView implements ViewInterface
{
    private ActionView $action;

    public function __construct(
        private AdminInterface $admin,
        private Template $template,
        private array $fields = [],
        private array $forms = []
    ) {
        $this->action = new ActionView(
            $this->admin->getAction()->getName(),
            $this->admin->getAction()->getConfiguration()->toArray(),
            $this->admin->getAction()->getConfiguration()->getTitle(),
        );
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
        return $this->admin->getConfiguration()->getTitle();
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
