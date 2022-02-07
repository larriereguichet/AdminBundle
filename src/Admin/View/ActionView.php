<?php

namespace LAG\AdminBundle\Admin\View;

class ActionView
{
    public function __construct(
        private string $name,
        private array $configuration,
        private string $title
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
