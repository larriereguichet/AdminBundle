<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event\Events;

use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Exception\Action\MissingActionException;

class RequestEvent extends AbstractEvent
{
    private ActionInterface $action;

    public function getAction(): ActionInterface
    {
        if (!isset($this->action)) {
            throw new MissingActionException('The current action is not set');
        }

        return $this->action;
    }

    public function setAction(ActionInterface $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function hasAction(): bool
    {
        return isset($this->action);
    }
}
