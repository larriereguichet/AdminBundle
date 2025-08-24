<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Metadata\Action;

// TODO Remove
interface ActionRendererInterface
{
    public function renderAction(Action $action, mixed $data = null): string;
}
