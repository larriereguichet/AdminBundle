<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Metadata\Action;

interface ActionRendererInterface
{
    public function renderAction(Action $action, mixed $data = null): string;
}
