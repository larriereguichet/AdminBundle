<?php

namespace LAG\AdminBundle\Field\Render;

use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\View\ViewInterface;

interface FieldRendererInterface
{
    public function render(FieldInterface $field, $entity): string;

    public function renderHeader(ViewInterface $admin, FieldInterface $field): string;
}
