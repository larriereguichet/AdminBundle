<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View;

/**
 * This is a value object to encapsulate Twig template values.
 */
class Template
{
    private string $template;
    private string $base;

    public function __construct(string $template, string $base)
    {
        $this->template = $template;
        $this->base = $base;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getBase(): string
    {
        return $this->base;
    }
}
