<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Component;

interface DynamicTemplateComponentInterface
{
    public function getTemplate(): ?string;
}
