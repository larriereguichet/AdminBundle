<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Action\ActionInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ActionEvent extends Event
{
    private ActionInterface $action;

    public function __construct(ActionInterface $action)
    {
        $this->action = $action;
    }

    public function getAction(): ActionInterface
    {
        return $this->action;
    }
}
