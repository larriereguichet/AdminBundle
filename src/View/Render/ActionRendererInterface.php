<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Render;

use LAG\AdminBundle\Resource\Metadata\Action;

interface ActionRendererInterface
{
    public function renderAction(Action $action, mixed $data): string;
}
