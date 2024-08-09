<?php

declare(strict_types=1);

namespace LAG\AdminBundle\View\Helper;

use LAG\AdminBundle\Resource\Metadata\Action;

interface RenderHelperInterface
{
    public function renderAction(Action $action, mixed $data): string;
}
