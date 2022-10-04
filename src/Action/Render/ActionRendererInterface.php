<?php

namespace LAG\AdminBundle\Action\Render;

use LAG\AdminBundle\Metadata\Action;

interface ActionRendererInterface
{
    public function render(Action $action, mixed $data = null): string;
}
