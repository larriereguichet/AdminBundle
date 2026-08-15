<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Component;

interface TemplateComponentInterface
{
    public function getTemplate(): ?string;
}
